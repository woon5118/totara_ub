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
 * @module editor_weka
 */

import Mention from 'editor_weka/components/nodes/Mention.vue';
import { shallowMount } from '@vue/test-utils';

describe('editor_weka/components/nodes/Mention.vue', function() {
  let wrapper;

  beforeAll(() => {
    wrapper = shallowMount(Mention, {
      mocks: {
        $url(url, params) {
          return `${url}?${params.toString()}`;
        },

        $str(id, component) {
          return `${id}-${component}`;
        },
      },

      propsData: {
        nodeInfo: {
          node: {
            attrs: {
              id: 15,
              display: 'Admin User',
            },
          },
        },
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
