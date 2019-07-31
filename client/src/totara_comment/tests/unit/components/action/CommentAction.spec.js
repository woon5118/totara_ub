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
import CommentAction from 'totara_comment/components/action/CommentAction';
import { shallowMount } from '@vue/test-utils';

jest.mock('tui/tui', () => null);

describe('totara_comment/components/action/CommentAction.vue', () => {
  let wrapper = null;

  beforeAll(() => {
    wrapper = shallowMount(CommentAction, {
      propsData: {
        totalReplies: 15,
        commentId: 5,
        area: 'comment',
        reacted: true,
        totalReactions: 100,
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
