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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @module engage_article
 */

import { shallowMount } from '@vue/test-utils';
import ArticleCardImage from 'engage_article/components/card/ArticleCardImage';

describe('engage_article/components/card/ArticleCardImage.vue', () => {
  let wrapper = null;

  beforeAll(() => {
    wrapper = shallowMount(ArticleCardImage, {
      mocks: {
        $str(identifier, component, param) {
          return `[${identifier}, ${component} - ${param}]`;
        },
        $url(str) {
          return str;
        },
      },

      propsData: {
        instanceId: 1,
        name: 'Hello world ',
        image: 'http://example.com/image.png',
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
