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

import Vue from 'vue';
import { mount } from '@vue/test-utils';
import Reform from 'tui/components/reform/Reform';
import { ReformScopeReceiver } from './util';

jest.mock('tui/pending');
jest.mock('tui/i18n');
jest.mock('tui/dom/focus', () => ({
  getTabbableElements(el) {
    return [el];
  },
}));

const validateWait = async () => {
  await Vue.nextTick(); // wait for nextTick like handleSubmit
  jest.advanceTimersByTime(20); // drain queue
  await Vue.nextTick(); // need 2 more nextTicks for some reason
  await Vue.nextTick();
};

const domSubmit = async wrapper => {
  wrapper.find('form').trigger('submit');
  await validateWait();
};

function createSimple(initialValues, { props, listeners } = {}) {
  const wrapper = mount(Reform, {
    propsData: {
      initialValues: initialValues,
      ...props,
    },
    listeners: {
      ...listeners,
    },
    scopedSlots: {
      default({ handleSubmit }) {
        const h = this.$createElement;
        return h('form', { on: { submit: handleSubmit } }, [
          h(ReformScopeReceiver),
        ]);
      },
    },
  });

  const fsr = wrapper.find(ReformScopeReceiver).vm;
  const vm = wrapper.vm;

  return {
    wrapper,
    vm,
    scope: fsr.reformScope,
    submit: async () => {
      return Promise.all([vm.submit(), validateWait()]);
    },
  };
}

describe('Reform', () => {
  beforeAll(() => {
    jest.useFakeTimers();
  });

  it('holds form state', async () => {
    const submit = jest.fn();
    const expectedResult = { a: 1, b: 2 };
    const wrapper = mount(Reform, {
      propsData: {
        initialValues: { a: 1, b: 2 },
      },
      listeners: { submit },
      scopedSlots: {
        default({ handleSubmit }) {
          const h = this.$createElement;
          return h('form', { on: { submit: handleSubmit } }, [
            h(ReformScopeReceiver),
          ]);
        },
      },
    });

    expect(wrapper.vm.values).toEqual(expectedResult);
    expect(submit).not.toHaveBeenCalled();
    await domSubmit(wrapper);
    expect(submit).toHaveBeenCalledWith(expectedResult);
  });

  it('allows updating form state via provide/inject callbacks', async () => {
    const { vm, scope } = createSimple({ a: 1 });

    expect(vm.values).toEqual({ a: 1 });

    scope.update('a', 2);
    scope.update('b', 3);
    scope.update(['c', 'd', 1, 'e'], 3);

    expect(vm.values).toEqual({
      a: 2,
      b: 3,
      c: { d: [undefined, { e: 3 }] },
    });

    expect(scope.getValue('a')).toBe(2);
    expect(scope.getValue('b')).toBe(3);
    expect(scope.getValue(['c', 'd', 1, 'e'])).toBe(3);
    expect(scope.getValue('c')).toEqual({ d: [undefined, { e: 3 }] });
  });

  it('marks fields as touched when they are blurred', () => {
    const { scope } = createSimple({ a: 1 });

    expect(scope.getTouched('a')).toBe(false);
    expect(scope.getTouched('b')).toBe(false);
    expect(scope.getTouched('c')).toBe(false);
    expect(scope.getTouched(['d', 'e'])).toBe(false);
    scope.update('a', 1);
    scope.update('b', 2);
    scope.update(['d', 'e'], 3);
    expect(scope.getTouched('a')).toBe(false);
    expect(scope.getTouched('b')).toBe(false);
    expect(scope.getTouched('c')).toBe(false);
    expect(scope.getTouched(['d', 'e'])).toBe(false);
    scope.blur('a');
    scope.blur('b');
    scope.blur('c');
    scope.blur(['d', 'e']);
    expect(scope.getTouched('a')).toBe(true);
    expect(scope.getTouched('b')).toBe(true);
    expect(scope.getTouched('c')).toBe(true);
    expect(scope.getTouched(['d', 'e'])).toBe(true);
    scope.$_internalUpdateSliceState('f', state => {
      Vue.set(state, 'values', { a: null });
      Vue.set(state, 'touched', { a: null });
      return state;
    });
    scope.blur(['f', 'a', 'example']);
    expect(scope.getTouched(['f', 'a', 'example'])).toBe(true);
  });

  it('validates fields according to registered validators', async () => {
    const { scope, submit } = createSimple({ a: 'no' });

    const validator1 = jest.fn(val => {
      const errors = {};
      if (val.a !== 'yes') errors.a = 'no 1';
      return errors;
    });

    const validator2 = jest.fn(val => {
      if (val !== 'yes') return 'no 2';
    });

    scope.register('validator', null, validator1);

    // errors only display if touched
    expect(scope.getError('a')).toBe(undefined);
    scope.blur('a');

    await validateWait();

    expect(scope.getError('a')).toBe('no 1');

    await submit();
    expect(scope.getError('a')).toBe('no 1');

    scope.register('validator', 'a', validator2);
    await submit();
    expect(scope.getError('a')).toBe('no 2'); // most specific wins

    scope.unregister('validator', null, validator1);
    scope.unregister('validator', 'a', validator2);
    scope.register('validator', 'a', validator2);
    scope.register('validator', null, validator1);
    await submit();
    expect(scope.getError('a')).toBe('no 2'); // registration order does not matter
  });

  it('allows passing root validator prop', async () => {
    const rootValidator = jest.fn(values => {
      const errors = {};
      if (values.a !== 2) errors.a = 'a must be 2';
      return errors;
    });

    const { scope, submit } = createSimple(
      {},
      { props: { validate: rootValidator } }
    );

    scope.update('a', 1);

    await submit();

    expect(scope.getError('a')).toBe('a must be 2');
  });

  it('merges deep error paths', async () => {
    const { scope, submit } = createSimple();

    scope.register('validator', 'a', () => ({ b: 'err1', c: { d: 'err2' } }));
    scope.register('validator', ['a', 'c'], () => ({ e: 'err3' }));

    await submit();

    expect(scope.getError(['a', 'b'])).toBe('err1');
    expect(scope.getError(['a', 'c', 'd'])).toBe('err2');
    expect(scope.getError(['a', 'c', 'e'])).toBe('err3');
  });

  it('can validate deeply nested paths', async () => {
    const { scope, submit } = createSimple({ a: [undefined, { b: 'no' }] });

    const validator = jest.fn(val => {
      if (val !== 'yes') {
        return 'must be yes';
      }
    });

    scope.register('validator', ['a', 2, 'b'], validator);
    await submit();
    expect(scope.getError(['a', 2, 'b'])).toBe('must be yes');
  });

  it('only runs required validators', async () => {
    const { scope } = createSimple({ a: 'no' });

    const validator1 = jest.fn(val => {
      const errors = {};
      if (val.a !== 'yes') errors.a = 'no 1';
      return errors;
    });

    const validator2 = jest.fn(val => {
      if (val !== 'yes') return 'no 2';
    });

    const validator3 = jest.fn(val => {
      if (val !== 'yes') return 'no 3';
    });

    scope.register('validator', null, validator1);
    scope.register('validator', 'a', validator2);
    scope.register('validator', 'b', validator3);

    await validateWait();

    expect(validator1).toHaveBeenCalled();
    expect(validator2).toHaveBeenCalled();
    expect(validator3).toHaveBeenCalled();

    [validator1, validator2, validator3].forEach(x => x.mockReset());

    scope.blur('a');

    await validateWait();

    expect(validator1).toHaveBeenCalled();
    expect(validator2).toHaveBeenCalled();
    expect(validator3).not.toHaveBeenCalled();
  });

  it('allows passing external errors via errors object', async () => {
    const rootValidator = jest.fn(values => {
      const errors = {};
      if (values.a !== 2) errors.a = 'a must be 2';
      return errors;
    });

    const { wrapper, scope, submit } = createSimple(
      {},
      { props: { validate: rootValidator, errors: { c: 'no c' } } }
    );

    scope.update('a', 1);
    scope.update('b', 1);

    await submit();

    expect(scope.getError('a')).toBe('a must be 2');
    expect(scope.getError('b')).toBe(undefined);
    expect(scope.getError('c')).toBe('no c');

    wrapper.setProps({ errors: { a: 'server error', b: 'b is required' } });
    await validateWait();

    expect(scope.getError('a')).toBe('a must be 2');
    expect(scope.getError('b')).toBe('b is required');
    expect(scope.getError('c')).toBe(undefined);

    wrapper.setProps({ errors: null });
    await validateWait();

    expect(scope.getError('a')).toBe('a must be 2');
    expect(scope.getError('b')).toBe(undefined);
    expect(scope.getError('c')).toBe(undefined);
  });

  it('allows adding hooks for processing submitted data', async () => {
    const handleSubmit = jest.fn();
    const { scope, submit } = createSimple(
      { a: { b: 3 } },
      { listeners: { submit: handleSubmit } }
    );

    const procRoot = val => {
      val.root = 1;
      return val;
    };
    scope.register('processor', null, procRoot);

    const procA = val => {
      val.q = 1;
      return val;
    };
    scope.register('processor', 'a', procA);

    const procAB = val => {
      return val + 1;
    };
    scope.register('processor', ['a', 'b'], procAB);

    const submitRoot = jest.fn();
    scope.register('submitHandler', null, submitRoot);

    const submitA = jest.fn();
    scope.register('submitHandler', 'a', submitA);

    const submitAB = jest.fn();
    scope.register('submitHandler', ['a', 'b'], submitAB);

    await submit();

    expect(submitAB).toHaveBeenCalledWith(4);
    expect(submitAB).toHaveBeenCalledBefore(submitA);
    expect(submitA).toHaveBeenCalledWith({ b: 4, q: 1 });
    expect(submitA).toHaveBeenCalledBefore(submitRoot);
    expect(submitRoot).toHaveBeenCalledWith({ a: { b: 4, q: 1 }, root: 1 });
    expect(submitRoot).toHaveBeenCalledBefore(handleSubmit);
    expect(handleSubmit).toHaveBeenCalledWith({ a: { b: 4, q: 1 }, root: 1 });

    scope.unregister('processor', null, procRoot);
    scope.unregister('processor', 'a', procA);
    scope.unregister('processor', ['a', 'b'], procAB);

    await submit();

    expect(handleSubmit).toHaveBeenCalledWith({ a: { b: 3 } });
  });

  it('focuses invalid inputs', async () => {
    const { scope, submit } = createSimple({ a: 'no' });

    const validator = jest.fn(val => {
      if (val !== 'yes') return 'no';
    });

    scope.register('validator', 'a', validator);

    const aEl = document.createElement('input');
    // hack, querySelectorAll doesn't seem to be working properly
    Object.defineProperty(aEl, 'querySelectorAll', {
      value: () => [aEl],
      enumerable: false,
    });
    const focusHandler = jest.fn();
    aEl.addEventListener('focus', focusHandler);
    scope.register('element', 'a', () => aEl);

    await submit();

    expect(focusHandler).toHaveBeenCalled();
  });
});
