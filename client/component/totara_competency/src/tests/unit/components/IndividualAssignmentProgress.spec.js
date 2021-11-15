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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @module totara_competency
 */

import { shallowMount } from '@vue/test-utils';
import IAP from 'totara_competency/components/IndividualAssignmentProgress';

describe('individualAssignmentProgress', () => {
  let props = null;

  beforeEach(() => {
    props = {
      assignmentProgress: {
        items: [
          {
            competency: {
              fullname: 'some name',
            },
            my_value: {
              percentage: 5,
              name: 'simple',
            },
            min_value: {
              percentage: 0,
              name: 'junk',
            },
            max_value: { percentage: 100 },
          },
        ],
      },
      userId: 5,
      isCurrentUser: true,
    };
  });

  it('Labels work as expected', () => {
    let wrapper = shallowMount(IAP, {
      propsData: props,
    });

    expect(wrapper.vm.labels).toHaveLength(1);
    expect(wrapper.vm.labels[0]).toBe('some name');

    let name = 'some name repeated'.repeat(50);
    let props2 = Object.assign({}, props);
    props2.assignmentProgress.items.push({
      competency: {
        fullname: name,
      },
      my_value: {
        percentage: 5,
        name: 'simple',
      },
      min_value: {
        percentage: 0,
        name: 'junk',
      },
      max_value: { percentage: 100 },
    });

    wrapper = shallowMount(IAP, {
      propsData: props2,
    });

    expect(wrapper.vm.labels).toHaveLength(2);
    expect(wrapper.vm.labels[1]).toBe(name);
  });

  it('shorten works as expected', () => {
    const wrapper = shallowMount(IAP, {
      propsData: props,
    });

    let short = wrapper.vm.shorten('125', 20);
    expect(short).toBeArrayOfSize(1);
    expect(short[0]).toBe('125');

    let middle = wrapper.vm.shorten('1234 56789', 10);
    expect(middle).toBeArrayOfSize(1);
    expect(middle[0]).toBe('1234 56789');

    let middle2 = wrapper.vm.shorten('1234 56789 abc', 10);
    expect(middle2).toBeArrayOfSize(2);
    expect(middle2[0]).toBe('1234 56789');
    expect(middle2[1]).toBe('abc');

    let strings = wrapper.vm.shorten('12345 6789 1234 12345', 10);
    expect(strings).toBeArrayOfSize(2);
    expect(strings[0]).toBe('12345 6789');
    expect(strings[1]).toBe('1234 12345');

    let lorem =
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    let long = wrapper.vm.shorten(lorem, 28);
    expect(long[0]).toBe('Lorem ipsum dolor sit amet,');
    expect(long[1]).toBe(
      'consectetur adipiscing elit,' + String.fromCharCode(8230)
    );
  });
});
