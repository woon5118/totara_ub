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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @module pathway_manual
 */

import { shallowMount } from '@vue/test-utils';
import component from 'pathway_manual/components/RatingCell';
import { mocks } from './mocks';
let wrapper;

const props = {
  scale: {},
  scaleValueId: '1',
  compId: '123',
  comment: 'Test comment',
  competency: 'Test competency',
};

describe('components/RatingCell.vue', () => {
  it('Checks snapshot', () => {
    wrapper = shallowMount(component, { mocks: mocks, propsData: props });
    expect(wrapper.element).toMatchSnapshot();
  });
});
