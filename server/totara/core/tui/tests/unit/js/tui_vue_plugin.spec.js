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

import tuiVuePlugin from 'totara_core/tui_vue_plugin';

let Vue;
let vm;

beforeEach(() => {
  Vue = jest.fn();
  Vue.directive = jest.fn();
  Vue.component = jest.fn();
  tuiVuePlugin.install(Vue);
  vm = new Vue();
});

describe('Vue#uid', () => {
  it('returns a consistent per-component string', () => {
    const inst1 = new Vue();
    expect(inst1.uid).toBe(inst1.uid);
    const inst2 = new Vue();
    expect(inst2.uid).toBe(inst2.uid);
    expect(inst1.uid).not.toBe(inst2.uid);
  });
});

test('Vue#$id() returns uid appended with a string', () => {
  expect(vm.$id()).toBe(vm.uid);
  expect(vm.$id('foo')).toBe(vm.uid + '-foo');
  expect(vm.$idRef('foo')).toBe('#' + vm.uid + '-foo');
});

test('Vue#$window points to window global if window defined', () => {
  const vm = new Vue();
  expect(vm.$window).toBe(window);
});
