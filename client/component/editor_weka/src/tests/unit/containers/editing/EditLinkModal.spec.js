/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @module editor_weka
 */

import { mount } from '@vue/test-utils';
import EditLinkModal from 'editor_weka/components/editing/EditLinkModal';

/**
 * @return {Wrapper<Vue>}
 * @param url {string}
 * @param save {function}
 */
const factory = (url, save) => {
  return mount(EditLinkModal, {
    propsData: {
      isNew: false,
      attrs: Object.assign({ type: 'link', text: 'link text' }, { url }),
      save,
      isMedia: () => {},
    },
    mocks: {},
  });
};

describe('editor_weka/components/editing/EditLinkModal.vue', () => {
  it('Should fix links if they are invalid', () => {
    const cases = [
      ['www.example.com', 'http://www.example.com'], // No scheme, add it.
      ['http://www.example.com', 'http://www.example.com'], // Has a scheme, leave it.
      ['https://www.example.com', 'https://www.example.com'], // Has a scheme, leave it.
      ['/relative/on/purpose', '/relative/on/purpose'], // Looks relative on purpose, leave it.
      ['#hash-on-purpose', '#hash-on-purpose'], // Looks like a hash fragment on purpose, leave it.
    ];

    const test = ([inLink, expectedOutLink]) => {
      const save = attrs => {
        expect(attrs.url).toEqual(expectedOutLink);
      };

      const wrapper = factory(inLink, save);

      wrapper.find('.tui-formBtn--prim').trigger('click'); // Click done to trigger save.
    };

    cases.forEach(test);
  });
});
