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

import { mount } from '@vue/test-utils';
import { plainWrapperArray } from '../../util';
import PropsProvider from 'totara_core/components/util/PropsProvider';

const SecondLevel = {
  props: ['j'],
  render(h) {
    return h('span', {}, [`j: ${this.j}`]);
  },
};

const TestComp = {
  props: ['i', 'j'],
  render(h) {
    return h('p', {}, [`${this.i}, ${this.j}`, h(SecondLevel)]);
  },
};

const PropsProviderWrap = {
  functional: true,
  render(h, { props, children }) {
    return h('div', [h(PropsProvider, { props }, children)]);
  },
};

describe('presentation/util/PropsProvider.vue', () => {
  it('passes props and events to direct children', () => {
    const handler = jest.fn();
    const wrapper = mount(PropsProviderWrap, {
      context: {
        children: [
          h => h(TestComp, { props: { i: 1 } }),
          'hi',
          h => h(TestComp, { props: { i: 2 } }),
          h => h(TestComp, { props: { i: 3, j: 3 } }),
        ],
      },
      propsData: {
        provide({ props }) {
          return {
            props: { j: (props.i || 0) + 1000 },
            listeners: { click: handler },
          };
        },
      },
    });

    expect(
      plainWrapperArray(wrapper.findAll(TestComp)).map(w => w.props())
    ).toMatchObject([
      { i: 1, j: 1001 },
      { i: 2, j: 1002 },
      { i: 3, j: 3 },
    ]);
    expect(
      plainWrapperArray(wrapper.findAll(SecondLevel)).map(w => w.props())
    ).toMatchObject([{ j: undefined }, { j: undefined }, { j: undefined }]);

    expect(handler).toHaveBeenCalledTimes(0);
    wrapper.find(TestComp).vm.$emit('click', 'foo');
    expect(handler).toHaveBeenCalledWith('foo');

    expect(wrapper.element).toMatchSnapshot();
  });

  it('merges multiple event handlers', () => {
    const handlerInline = jest.fn();
    const handlerProvided = jest.fn();
    const wrapper = mount(PropsProviderWrap, {
      context: {
        children: [
          h => h(TestComp, { props: { i: 1 }, on: { click: handlerInline } }),
          h => h(TestComp, { props: { i: 2 }, on: { click: handlerInline } }),
          h => h(TestComp, { props: { i: 3 } }),
        ],
      },
      propsData: {
        provide({ props }) {
          return {
            props: { j: (props.i || 0) + 1000 },
            listeners: props.i != 2 ? { click: handlerProvided } : {},
          };
        },
      },
    });

    const comps = wrapper.findAll(TestComp);

    comps.at(0).vm.$emit('click', 'first');
    expect(handlerInline).toHaveBeenCalledWith('first');
    expect(handlerProvided).toHaveBeenCalledWith('first');

    comps.at(1).vm.$emit('click', 'second');
    expect(handlerInline).toHaveBeenCalledWith('second');
    expect(handlerProvided).not.toHaveBeenCalledWith('second');

    comps.at(2).vm.$emit('click', 'third');
    expect(handlerInline).not.toHaveBeenCalledWith('third');
    expect(handlerProvided).toHaveBeenCalledWith('third');
  });
});
