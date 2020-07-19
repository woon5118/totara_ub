/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module tui
 */

import Vue from 'vue';
import {
  get,
  set,
  orderBy,
  isPlainObject,
  structuralDeepClone,
  structuralShallowClone,
  result,
} from 'tui/util';
import { set as vueSet } from 'tui/vue_util';
import { isLangString, loadLangStrings, isRtl } from 'tui/i18n';
import { getDocumentPosition } from 'tui/dom/position';
import { getTabbableElements } from 'tui/dom/focus';
import BatchingSerialLoadQueue from '../../js/internal/BatchingSerialLoadQueue';

/**
 * Check if two arrays are shallowly == (all of their items are ==)
 *
 * @param {array} a
 * @param {array} b
 * @returns {boolean}
 */
const arrayEqual = (a, b) => a.length == b.length && arrayStartsWith(a, b);

/**
 * Check if an array starts with a prefix (using ==)
 *
 * @param {array} arr
 * @param {array} prefix
 * @returns {boolean}
 */
const arrayStartsWith = (arr, prefix) => prefix.every((x, i) => arr[i] == x);

/**
 * Ensure path-like value is a path.
 *
 * @param {(array|string)} path
 * @returns {array}
 */
const toPath = path => (Array.isArray(path) ? path : [path]);

/**
 * Helper to check if value is a plain data structure (object or array).
 *
 * @param {*} value
 * @returns {boolean}
 */
const isDataStructure = value => isPlainObject(value) || Array.isArray(value);

export default {
  provide() {
    return {
      reformScope: {
        getValue: name => get(this.values, name),
        getError: name => get(this.displayedErrors, name),
        getTouched: name => !!get(this.touched, name),
        update: this.update,
        blur: this.blur,
        register: this.register,
        unregister: this.unregister,
        updateRegistration: this.updateRegistration,
        getInputName: this.getInputName,
        $_internalUpdateSliceState: this.$_internalUpdateSliceState,
      },
    };
  },

  props: {
    /**
     * Initial values for form fields.
     */
    initialValues: {
      type: [Object, Function],
      default: () => ({}),
    },

    /**
     * External errors to display in form.
     */
    errors: Object,

    /**
     * Root-level validator function.
     */
    validate: Function,

    /**
     * Validation mode.
     *
     * 'auto': smart validation
     * 'submit': only validate on submit
     */
    validationMode: {
      type: String,
      default: 'auto',
      validator: x => ['auto', 'submit'].includes(x),
    },
  },

  data() {
    return {
      // structural state
      validators: [],
      registrations: {
        processor: [],
        submitHandler: [],
        element: [],
      },

      // form state
      submitting: false,

      // form content state
      values: structuralDeepClone(result(this.initialValues)),
      touched: {},
      changed: {},

      // generated
      mergedErrors: {},
      validatorsErrors: [],
    };
  },

  computed: {
    /**
     * Get displayed errors.
     *
     * @returns {object}
     */
    displayedErrors() {
      return this.$_onlyTouched(this.mergedErrors, this.touched);
    },

    /**
     * Work out if we have any errors.
     *
     * @returns {boolean}
     */
    isValid() {
      return this.$_collectErrorValues(this.mergedErrors).every(x => !x);
    },
  },

  watch: {
    // revalidate when external errors change
    errors: {
      handler(errors) {
        if (errors) {
          this.$_mergeErrors(this.touched, this.$_makeAllTouch(errors));
        }
        this.$_validate();
      },
      deep: true,
    },
  },

  created() {
    this.validationQueue = new BatchingSerialLoadQueue({
      handler: this.$_validateInternal,
      wait: 10,
      equal: arrayEqual,
    });
  },

  methods: {
    /**
     * Update recorded value for input.
     *
     * @param {(string|number|array)} path
     * @param {*} value
     */
    update(path, value) {
      vueSet(this.values, path, value);
      this.$emit('change', this.values);
      if (this.validationMode != 'submit') {
        this.$_validate(path);
      }
    },

    /**
     * Record that input has blurred (been unfocused).
     *
     * @param {(string|number|array)} path
     */
    blur(path) {
      vueSet(this.touched, path, true);
      if (this.validationMode != 'submit') {
        this.$_validate(path);
      }
    },

    /**
     * Register (path, function) of specified type.
     *
     * @param {('validator'|'processor'|'submitHandler')} type
     * @param {(string|number|array|null)} path
     * @param {function} fn
     */
    register(type, path, fn) {
      switch (type) {
        case 'validator':
          this.$_register(this.validators, path, fn);
          if (this.validationMode != 'submit') {
            this.$_validateIfTouched(path);
          }
          return;
        default:
          if (!this.registrations[type]) {
            this.registrations[type] = [];
          }
          return this.$_register(this.registrations[type], path, fn);
      }
    },

    /**
     * Unregister (path, function) of specified type.
     *
     * @param {('validator'|'processor'|'submitHandler')} type
     * @param {(string|number|array|null)} path
     * @param {function} fn
     */
    unregister(type, path, fn) {
      switch (type) {
        case 'validator':
          this.$_unregister(this.validators, path, fn);
          if (this.validationMode != 'submit') {
            this.$_validateIfTouched(path);
          }
          return;
        default:
          if (!this.registrations[type]) {
            return;
          }
          return this.$_unregister(this.registrations[type], path, fn);
      }
    },

    /**
     * Helper for updating registration when it changes.
     *
     * Unregisters the old function and registers the new one.
     * Does nothing if they haven't changed.
     *
     * @param {string} type
     * @param {(string|number|array)} path
     * @param {function} validator
     * @param {(string|number|array)} oldPath
     * @param {function} oldValidator
     */
    updateRegistration(type, path, fn, oldPath, oldFn) {
      if (
        fn == oldFn &&
        (path == oldPath || arrayEqual(toPath(path), toPath(oldPath)))
      ) {
        // nothing has changed
        return;
      }

      if (oldFn) {
        this.unregister(type, oldPath, oldFn);
      }

      if (fn) {
        this.register(type, path, fn);
      }
    },

    $_register(array, path, fn) {
      const entry = [path, fn];
      if (!array.some(x => this.$_pathFunctionEqual(x, entry))) {
        array.push(entry);
      }
    },

    $_unregister(array, path, fn) {
      const entry = [path, fn];
      const index = array.findIndex(x => this.$_pathFunctionEqual(x, entry));
      if (index !== -1) {
        array.splice(index, 1);
      }
    },

    /**
     * Get name to use for HTML input.
     *
     * This is mostly for autocomplete support.
     *
     * e.g.:
     * ['name'] => 'name'
     * ['a', 'b', 'c'] => 'a[b][c]'
     *
     * This syntax was chosen as it is the one used by PHP for nested params.
     *
     * @param {(array|string)} path
     * @returns {string}
     */
    getInputName(path) {
      return toPath(path)
        .map((part, i) => (i == 0 ? part : '[' + part + ']'))
        .join('');
    },

    /**
     * Reset form to initial state.
     */
    reset() {
      this.values = structuralDeepClone(result(this.initialValues));
      this.touched = {};
      this.mergedErrors = {};
      this.validatorsErrors = [];
    },

    /**
     * Handle submit event on form.
     *
     * @param {Event} e
     */
    async handleSubmit(e) {
      e.preventDefault();

      return this.submit();
    },

    /**
     * Submit form.
     */
    async submit() {
      this.submitting = true;

      // wait for rerender
      await Vue.nextTick();

      // validate
      await this.$_validate();

      this.submitting = false;
      this.$_mergeErrors(this.touched, this.$_makeAllTouch(this.mergedErrors));

      // emit
      if (this.isValid) {
        const processors = this.$_sortEntriesByPath(
          this.registrations.processor
        ).reverse();
        const submitHandlers = this.$_sortEntriesByPath(
          this.registrations.submitHandler
        ).reverse();
        let values = structuralDeepClone(this.values);

        // process values
        for (let i = 0; i < processors.length; i++) {
          const [path, processor] = processors[i];
          if (path === null) {
            // eslint false positive (`values` is not modifiable from outside
            // this function while we are awaiting):
            // eslint-disable-next-line require-atomic-updates
            values = await processor(values);
          } else {
            const result = await processor(get(values, path));
            set(values, path, result);
          }
        }

        // call registered submit handlers
        submitHandlers.forEach(([path, handler]) =>
          handler(path === null ? values : get(values, path))
        );

        this.$emit('submit', values);
      } else {
        this.$_focusFirstInvalid();
      }
    },

    /**
     * Focus first invalid field.
     *
     * @internal
     */
    $_focusFirstInvalid() {
      const rtl = isRtl();
      const isLeftBefore = rtl ? (a, b) => a > b : (a, b) => a < b;

      // find first invalid field (by physical position in document)
      let firstEl = null;
      let firstPos = null;
      this.registrations.element.forEach(([path, getEl]) => {
        const el = getEl();
        if (el && get(this.mergedErrors, path)) {
          const pos = getDocumentPosition(el);
          if (
            firstEl == null ||
            pos.top < firstPos.top ||
            (pos.top === firstPos.top && isLeftBefore(pos.left, firstPos.left))
          ) {
            firstEl = el;
            firstPos = pos;
          }
        }
      });

      if (firstEl) {
        const tabbable = getTabbableElements(firstEl);
        if (tabbable.length > 0) {
          tabbable[0].focus();
        } else {
          firstEl.scrollIntoView({ behavior: 'smooth' });
        }
      }
    },

    /**
     * INTERNAL method for updating a slice of form state.
     *
     * Do not use outside of totara_core.
     *
     * @internal
     * @param {(string|number|array)} path
     * @param {function} fn Callback. Called with { values, touched }, should return the same shaped object.
     */
    $_internalUpdateSliceState(path, fn) {
      const slice = path
        ? { values: get(this.values, path), touched: get(this.touched, path) }
        : { values: this.values, touched: this.touched };

      const result = fn(slice);

      if (path) {
        vueSet(this.values, path, result.values);
        vueSet(this.touched, path, result.touched);
      } else {
        this.values = result.values;
        this.touched = result.touched;
      }

      this.$emit('change', this.values);

      if (this.validationMode != 'submit') {
        this.$_validate(path);
      }
    },

    /**
     * Check if two path-function entries are equal.
     *
     * @param {function} a
     * @param {function} b
     * @returns {bool}
     */
    $_pathFunctionEqual(a, b) {
      return arrayEqual(toPath(a[0]), toPath(b[0])) && a[1] == b[1];
    },

    /**
     * Sort [path, value] entries by the length of the path (shortest to longest).
     *
     * @internal
     * @param {array} arr
     * @returns {array}
     */
    $_sortEntriesByPath(arr) {
      return orderBy(arr, ([path]) => (path ? path.length : 0));
    },

    /**
     * Run validators and update error status.
     *
     * @internal
     * @param {(array|null)} [validatePath]
     * @returns {Promise}
     */
    $_validate(path = null) {
      return this.validationQueue.enqueue([path]);
    },

    /**
     * Run validators if path touched.
     *
     * @internal
     * @param {(array|null)} [validatePath]
     */
    $_validateIfTouched(path = null) {
      if (path == null) {
        this.$_validate();
      } else if (get(this.touched, path)) {
        this.$_validate(path);
      }
    },

    /**
     * Run validators for specified paths (called by queue).
     *
     * @internal
     * @param {array} validatePaths
     */
    async $_validateInternal(validatePaths) {
      validatePaths = validatePaths.map(toPath);
      // figure out what validators to run
      let validators = this.$_sortEntriesByPath(this.validators);
      if (this.validate) {
        validators.unshift([null, this.validate]);
      }
      const validateRoot = validatePaths.some(
        x => x.length == 1 && x[0] == null
      );
      let validatorMatcher;
      if (!validateRoot) {
        // run validators for any parents, self, or children matching
        // root validators always run
        const validatorMatcherInner = (path, reqPath) =>
          path == null ||
          arrayStartsWith(toPath(path), reqPath) ||
          arrayStartsWith(reqPath, toPath(path));

        validatorMatcher = path =>
          validatePaths.some(reqPath => validatorMatcherInner(path, reqPath));

        validators = validators.filter(([path]) => validatorMatcher(path));
      }

      // run validators (async)
      let validatorResults = await Promise.all(
        validators.map(([path, validator]) => {
          if (path === null) {
            return Promise.resolve(validator(this.values)).then(x => [path, x]);
          } else {
            const values = get(this.values, path);
            return Promise.resolve(validator(values)).then(validatorResult => {
              let validatorErrors = {};
              vueSet(validatorErrors, path, validatorResult);
              return [path, validatorErrors];
            });
          }
        })
      );

      if (validatorMatcher) {
        // filter out errors from validators matching our path, then replace
        // with new validation results
        validatorResults = this.$_sortEntriesByPath(
          this.validatorsErrors
            .filter(([path]) => !validatorMatcher(path))
            .concat(validatorResults)
        );
      }

      // combine errors into a single object
      const mergedErrors = validatorResults.reduce(
        (acc, [, errors]) => this.$_mergeErrors(acc, errors, false),
        this.errors ? structuralDeepClone(this.errors) : {}
      );

      // load strings for errors
      const langStrings = this.$_collectLangStrings(mergedErrors);
      if (langStrings.length > 0) {
        await loadLangStrings(langStrings);
      }

      // finally, assign result
      this.validatorsErrors = validatorResults;
      this.mergedErrors = mergedErrors;
    },

    /**
     * Merge error objects.
     *
     * @internal
     * @param {object} result Error result. Will be mutated.
     * @param {object} newErrors Errors to merge in.
     * @param {bool} keepResult Give properties already on result priority?
     */
    $_mergeErrors(result, newErrors, keepResult) {
      if (!newErrors) {
        return result;
      }
      Object.keys(newErrors).forEach(k => {
        const val = newErrors[k];
        if (!val) return;
        if (k in result && result[k] != null) {
          if (isDataStructure(val)) {
            this.$_mergeErrors(result[k], val, keepResult);
          } else {
            if (!keepResult) {
              // Vue.set not needed as already in result
              result[k] = val;
            }
          }
        } else {
          // doesn't exist in result - just assign
          // shallow clone so later updates to the same property don't modify
          // the value from newErrors
          Vue.set(result, k, structuralShallowClone(val));
        }
      });
      return result;
    },

    /**
     * Filter nested errors object to keys that have a truthy value in touched.
     *
     * @internal
     * @param {object} errors
     * @param {object} touched
     * @returns {object}
     */
    $_onlyTouched(errors, touched) {
      return Object.entries(errors)
        .filter(([key]) => touched[key])
        .reduce(
          (acc, [key, value]) => {
            acc[key] = isDataStructure(value)
              ? this.$_onlyTouched(value, touched[key])
              : value;
            return acc;
          },
          Array.isArray(errors) ? [] : {}
        );
    },

    /**
     * Make a touch object that covers all entries in errors.
     *
     * @internal
     * @param {(object|array)} errors
     * @returns {(object|array)}
     */
    $_makeAllTouch(errors) {
      return Object.entries(errors).reduce(
        (acc, [key, value]) => {
          acc[key] = isDataStructure(value) ? this.$_makeAllTouch(value) : true;
          return acc;
        },
        Array.isArray(errors) ? [] : {}
      );
    },

    /**
     * Collect all values from an error object.
     *
     * @internal
     * @param {(object|array)} errors
     */
    $_collectErrorValues(errors) {
      const arr = [];
      this.$_collectErrorValuesInternal(arr, errors);
      return arr;
    },

    /**
     * Internal implementation of $_collectErrorValues
     *
     * @internal
     * @param {array} arr Output
     * @param {(object|array)} errors
     */
    $_collectErrorValuesInternal(arr, errors) {
      return Object.values(errors).forEach(value => {
        if (isDataStructure(value)) {
          this.$_collectErrorValuesInternal(arr, value);
        } else if (value) {
          arr.push(value);
        }
      });
    },

    /**
     * Collect all language strings from an error object.
     *
     * @internal
     * @param {array} arr Output array
     * @param {(object|array)} errors
     */
    $_collectLangStrings(errors) {
      return this.$_collectErrorValues(errors).filter(x => isLangString(x));
    },

    /**
     * Get whether the form is submitting.
     *
     * @returns {boolean}
     */
    getSubmitting() {
      return this.submitting;
    },
  },

  render() {
    // do not return any state directly here, return getter functions instead.
    // otherwise if this component is wrapped, Vue will think the state is being
    // accessed when we pass along all slot props.
    return this.$scopedSlots.default({
      getSubmitting: this.getSubmitting,
      handleSubmit: this.handleSubmit,
      reset: this.reset,
    });
  },
};
