/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

/* eslint-disable tui/no-tui-internal */

import Vue from 'vue';
import tui from 'totara_core/tui';
import TotaraModuleStore from 'totara_core/internal/TotaraModuleStore';
import requirements from 'totara_core/internal/requirements';

jest.mock('totara_core/config');
jest.mock('totara_core/apollo_client', () => null);
jest.mock('totara_core/internal/TotaraModuleStore');
jest.mock('totara_core/internal/requirements');

const modules = TotaraModuleStore.mock.instances[0];

const mockComp = name => {
  if (name == 'invalid') {
    const error = new Error("Cannot find module '" + name + "'");
    error.code = 'MODULE_NOT_FOUND';
    throw error;
  }
  if (name == 'totara_core/components/errors/ErrorBoundary') {
    return {
      $_name: name,
      render() {
        return this.$scopedSlots.default();
      },
    };
  }
  return {
    $_name: name,
    render: h => h('div', name),
  };
};

modules.require.mockImplementation(name => ({
  default: mockComp(name),
  __esModule: true,
}));
modules.import.mockImplementation(async name => modules.require(name));
modules.default.mockImplementation(m => (m.__esModule ? m.default : m));
requirements.get.mockImplementation(() => ({ any: false }));

beforeEach(() => {
  modules.require.mockClear();
  requirements.get.mockClear();
});

test('Vue is exposed as tui.Vue', () => {
  expect(tui.Vue).toBe(Vue);
});

test('require proxies to modules.require', () => {
  tui.require('foo');
  expect(modules.require).toHaveBeenCalledWith('foo');
});

test('import proxies to modules.import', () => {
  tui.import('bar');
  expect(modules.import).toHaveBeenCalledWith('bar');
});

describe('mount', () => {
  async function mountHelper(comp, data) {
    const wrapper = document.createElement('div');
    const el = document.createElement('span');
    wrapper.append(el);
    await tui.mount(comp, data, el);
    return wrapper;
  }

  it('mounts the provided component', async () => {
    const wrapper = await mountHelper({ render: h => h('div', 'hello') });
    expect(wrapper.innerHTML).toBe('<div>hello</div>');
  });

  it('wraps the provided component in an error boundary', () => {
    expect.assertions(1);
    mountHelper({
      created() {
        let vm = this.$parent;
        while (vm) {
          if (
            vm.$options.$_name == 'totara_core/components/errors/ErrorBoundary'
          ) {
            expect(vm.$options.$_name).toBe(
              'totara_core/components/errors/ErrorBoundary'
            );
          }
          vm = vm.$parent;
        }
      },
      render: h => h('div', 'hello'),
    });
  });

  it('loads the component if it is a string', () => {
    mountHelper('comp');
    expect(modules.import).toHaveBeenCalledWith('comp');
  });

  it('loads requirements beforehand', () => {
    const comp = mockComp('comp');
    const reqs = { any: true, foo: true };
    requirements.get.mockImplementationOnce(() => reqs);
    requirements.load.mockImplementationOnce(async () => {});
    mountHelper(comp);
    expect(requirements.get).toHaveBeenCalledWith(comp);
    expect(requirements.load).toHaveBeenCalledWith(reqs);
  });
});

describe('needsRequirements', () => {
  it('checks requirements for provided component', () => {
    const comp = mockComp('comp');
    const reqs = { any: true, foo: true };
    requirements.get.mockImplementationOnce(() => reqs);
    expect(tui.needsRequirements(comp)).toBe(true);
    expect(requirements.get).toHaveBeenCalledWith(comp);
  });
});

describe('loadRequirements', () => {
  it('loads requirements for provided component', () => {
    const comp = mockComp('comp');
    const reqs = { any: true, foo: true };
    requirements.get.mockImplementationOnce(() => reqs);
    requirements.load.mockImplementationOnce(async () => {});
    tui.loadRequirements(comp);
    expect(requirements.get).toHaveBeenCalledWith(comp);
    expect(requirements.load).toHaveBeenCalledWith(reqs);
  });
});

describe('scan', () => {
  const tuiMount = tui.mount;
  beforeEach(() => {
    tui.mount = jest.fn(() => {});
  });
  afterEach(() => {
    tui.mount = tuiMount;
  });

  it('calls tui.mount for every component matching [data-tui-component]', () => {
    const originalConsole = global.console;
    global.console = {
      error: jest.fn(),
    };

    var el1 = document.createElement('div');
    var el2 = document.createElement('div');
    el2.setAttribute('data-tui-component', 'foo');
    el2.setAttribute('data-tui-props', '{"a":2}');
    el1.append(el2);
    var el3 = document.createElement('div');
    el3.setAttribute('data-tui-component', 'bar');
    el1.append(el3);
    var el4 = document.createElement('div');
    el4.setAttribute('data-tui-component', 'invalid');
    el1.append(el4);
    var el5 = document.createElement('div');
    el5.setAttribute('data-tui-component', 'baz');
    // invalid JSON - will trigger console.error call when scan runs
    el5.setAttribute('data-tui-props', '{a}');
    el1.append(el5);

    tui.scan(el1);

    expect(tui.mount).toHaveBeenCalledWith('foo', { props: { a: 2 } }, el2);
    expect(tui.mount).toHaveBeenCalledWith('bar', { props: null }, el3);

    expect(console.error).toHaveBeenCalledTimes(1);

    global.console = originalConsole;
  });

  it('finds elements in the document if no el is passed', () => {
    var el = document.createElement('div');
    el.setAttribute('data-tui-component', 'foo');
    el.setAttribute('data-tui-props', '{"a":2}');
    document.body.append(el);

    tui.scan();

    el.remove();

    expect(tui.mount).toHaveBeenCalledWith('foo', { props: { a: 2 } }, el);
  });
});

describe('tui._processOverride', () => {
  it('prevents inheriting when inheritable = false', () => {
    const comp = {};
    tui._processOverride(comp, { inheritable: false });
    expect(comp.extends).toEqual(undefined);
  });

  it('skips Vue.extend() parents', () => {
    const comp = {};
    tui._processOverride(comp, Vue.extend({}));
    expect(comp.extends).toEqual(undefined);
  });

  it('requires component to have __hasBlocks', () => {
    const comp = {};
    expect(() => tui._processOverride(comp, {})).toThrow(
      'components must be processed by tui-vue-loader'
    );
  });

  it('skips when component has its own script block', () => {
    const comp = { __hasBlocks: { script: true } };
    tui._processOverride(comp, {});
    expect(comp.extends).toEqual(undefined);
  });

  it('sets extends when everything is okay', () => {
    const comp = { __hasBlocks: {} };
    const parent = { foo: 5 };
    tui._processOverride(comp, parent);
    expect(comp.extends).toBe(parent);
  });

  it('does not set extends if it is already set', () => {
    const oldParent = {};
    const comp = { __hasBlocks: {}, extends: oldParent };
    const parent = { foo: 5 };
    tui._processOverride(comp, parent);
    expect(comp.extends).toBe(oldParent);
  });

  it('resolves parent as a module if passed a string', () => {
    modules.isEvaluating.mockReturnValueOnce(false);
    tui._processOverride({ __hasBlocks: {} }, 'foo');
    expect(modules.require).toHaveBeenCalledWith('foo');
  });

  it('calls modules._requirePrevious if evaluating the same module', () => {
    modules.isEvaluating.mockImplementationOnce(x => x == 'bar');
    modules._requirePrevious.mockReturnValueOnce({});
    tui._processOverride({ __hasBlocks: {} }, 'bar');
    expect(modules._requirePrevious).toHaveBeenCalledWith('bar');

    modules.isEvaluating.mockImplementationOnce(x => x == 'baz');
    modules._requirePrevious.mockReturnValueOnce(undefined);
    expect(() => tui._processOverride({ __hasBlocks: {} }, 'baz')).toThrow(
      'does not exist'
    );
  });
});

describe('tui.vueAssign', () => {
  const vueSet = Vue.set;
  beforeEach(() => {
    Vue.set = jest.fn((obj, key, value) => (obj[key] = value));
  });
  afterEach(() => {
    Vue.set = vueSet;
  });

  it('calls Vue.set for every key on every source, left to right', () => {
    const target = {};
    tui.vueAssign(target, { a: 1, b: 2 }, { c: 3 });
    expect(Vue.set).toHaveBeenNthCalledWith(1, target, 'a', 1);
    expect(Vue.set).toHaveBeenNthCalledWith(2, target, 'b', 2);
    expect(Vue.set).toHaveBeenNthCalledWith(3, target, 'c', 3);
  });

  it('later arguments take precedence', () => {
    const target = { a: 0 };
    const result = tui.vueAssign(target, { a: 1, b: 2 }, { a: 3 });
    expect(result).toEqual({ a: 3, b: 2 });
  });

  it('returns target if it is an object', () => {
    const target = {};
    expect(tui.vueAssign(target, { a: 1 })).toBe(target);
  });

  it('keys from prototype are not copied', () => {
    const source = Object.create({ a: 1 });
    source.b = 2;
    expect(tui.vueAssign({}, source)).toEqual({ b: 2 });
  });

  it('falsy sources are ignored', () => {
    expect(tui.vueAssign({}, false)).toEqual({});
  });

  it('throws exception on undefined or null target', () => {
    expect(() => tui.vueAssign(null, {})).toThrow();
    expect(() => tui.vueAssign(undefined, {})).toThrow();
  });
});
