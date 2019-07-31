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
 * @module engage_survey
 */

import SurveyForm from 'engage_survey/components/form/SurveyForm';
import { shallowMount } from '@vue/test-utils';

describe('engage_survey/components/form/SurveyForm.vue', function() {
  let wrapper = null;

  beforeAll(function() {
    wrapper = shallowMount(SurveyForm, {
      mocks: {
        $str(id, component) {
          return `${id}, ${component}`;
        },

        $id(random) {
          return `some-${random}`;
        },
      },
    });
  });

  it('Checks snapshot', function() {
    expect(wrapper.html()).toMatchSnapshot();
  });
});
