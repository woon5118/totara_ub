/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
 */

import { pick } from 'tui/util';
import { getPropDefs, getModelDef } from 'tui/vue_util';
import FormField from 'tui/components/uniform/FormField';

/**
 * Create a wrapper component for an input.
 *
 * The only hard requirement is that the input component works with v-model.
 *
 * @param {(object|Vue)} input Input component to wrap.
 * @param {object} [options]
 * @param {boolean} [options.passAriaLabelledby] Pass the label ID as aria-labelledby.
 * @returns {(object|Vue)} Vue component.
 */
export function createUniformInputWrapper(input, options = {}) {
  const inputProps = Object.assign({}, getPropDefs(input));
  delete inputProps.name;
  delete inputProps.validate;
  delete inputProps.validations;
  delete inputProps.id;
  delete inputProps.ariaDescribedby;
  delete inputProps.ariaInvalid;
  delete inputProps.ariaLabelledby;

  const props = Object.assign({}, inputProps, {
    name: {
      type: [String, Number, Array],
      required: true,
    },

    validate: Function,
    validations: [Function, Array],
  });

  const model = getModelDef(input);
  const modelProp = (model && model.prop) || 'value';
  const modelEvent = (model && model.event) || 'input';

  return {
    functional: true,

    // needed for lang string loader support
    components: {
      FormField,
      Input: input,
    },

    inheritAttrs: false,

    props: props,

    render(h, { props, children }) {
      const propsForInput = pick(props, Object.keys(inputProps));
      return h(FormField, {
        props: {
          name: props.name,
          validate: props.validate,
          validations: props.validations,
        },
        scopedSlots: {
          default: ({
            id,
            value,
            update,
            blur,
            inputName,
            errorId,
            labelId,
          }) => {
            const finalProps = Object.assign({}, propsForInput);
            finalProps[modelProp] = value;
            // attrs that are actually props will automatically be moved to
            // props by Vue
            const attrs = {
              id,
              name: inputName,
              'aria-describedby': errorId || null,
              'aria-invalid': errorId ? 'true' : null,
            };
            if (options.passAriaLabelledby) {
              attrs['aria-labelledby'] = labelId;
            }
            return h(
              input,
              {
                props: finalProps,
                attrs,
                on: {
                  [modelEvent]: value => update(value),
                  blur,
                },
              },
              children
            );
          },
        },
      });
    },
  };
}
