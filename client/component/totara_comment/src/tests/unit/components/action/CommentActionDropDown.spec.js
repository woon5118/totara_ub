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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @module totara_comment
 */

import CommentActionDropDown from 'totara_comment/components/action/CommentActionDropDown';
import { shallowMount } from '@vue/test-utils';

describe('totara_comment/components/action/CommentActionDropDown.vue', () => {
  let wrapper;

  beforeAll(() => {
    wrapper = shallowMount(CommentActionDropDown, {
      propsData: {
        deleteAble: true,
        editAble: true,
        reportAble: false,
      },
      mocks: {
        $str(x, y) {
          return `${x}-${y}`;
        },
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
