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

import User from 'editor_weka/components/suggestion/User';
import { mount } from '@vue/test-utils';

describe('editor_weka/components/suggestion/User.vue', function() {
  let wrapper = null;

  beforeAll(function() {
    wrapper = mount(User, {
      mocks: {
        $str(id, component) {
          return `${id}, ${component}`;
        },

        $id(random) {
          return `something-${random}`;
        },

        $apollo: {
          loading: false,
        },
      },

      propsData: {
        x: 1,
        y: 1,
        pattern: 'bolobala',
      },
    });
  });

  it('Checks snapshot', function() {
    expect(wrapper.element).toMatchSnapshot();
  });
});
