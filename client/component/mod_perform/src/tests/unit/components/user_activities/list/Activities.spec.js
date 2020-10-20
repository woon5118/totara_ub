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
 * @module mod_perform
 */

import { shallowMount } from '@vue/test-utils';
import component from 'mod_perform/components/user_activities/list/Activities';

const mocks = {
  $apollo: {
    loading: false,
  },
  $str: function(identifier, plugin) {
    return 'mock translated: ' + identifier + ',' + plugin;
  },
};

const props = {
  currentUserId: 111,
  about: 'self',
  viewUrl: 'some string',
  printUrl: 'some other string',
};

describe('Activities', () => {
  it('Checks status aggregation for multiple participant instances in getYourProgressText method', () => {
    const wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: props,
    });

    const expectedTextNotApplicable =
      'mock translated: user_activities_status_not_applicable,mod_perform';
    const expectedTextComplete =
      'mock translated: user_activities_status_complete,mod_perform';
    const expectedTextNotStarted =
      'mock translated: user_activities_status_not_started,mod_perform';
    const expectedTextInProgress =
      'mock translated: user_activities_status_in_progress,mod_perform';
    const expectedTextNotSubmitted =
      'mock translated: user_activities_status_not_submitted,mod_perform';

    const dataProvider = [
      {
        expected: expectedTextComplete,
        combinations: [
          [{ is_for_current_user: true, progress_status: 'COMPLETE' }],
          [
            { is_for_current_user: false, progress_status: 'NOT_STARTED' },
            { is_for_current_user: true, progress_status: 'COMPLETE' },
            { is_for_current_user: true, progress_status: 'COMPLETE' },
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            {
              is_for_current_user: true,
              progress_status: 'NOT_SUBMITTED',
            },
          ],
        ],
      },
      {
        expected: expectedTextNotStarted,
        combinations: [
          [{ is_for_current_user: true, progress_status: 'NOT_STARTED' }],
          [
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            {
              is_for_current_user: true,
              progress_status: 'NOT_SUBMITTED',
            },
            { is_for_current_user: true, progress_status: 'NOT_STARTED' },
            { is_for_current_user: true, progress_status: 'NOT_STARTED' },
            { is_for_current_user: false, progress_status: 'COMPLETE' },
          ],
        ],
      },
      {
        expected: expectedTextInProgress,
        combinations: [
          [{ is_for_current_user: true, progress_status: 'IN_PROGRESS' }],
          [
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            {
              is_for_current_user: true,
              progress_status: 'NOT_SUBMITTED',
            },
            { is_for_current_user: true, progress_status: 'NOT_STARTED' },
            { is_for_current_user: true, progress_status: 'COMPLETE' },
          ],
          [
            { is_for_current_user: true, progress_status: 'IN_PROGRESS' },
            { is_for_current_user: true, progress_status: 'COMPLETE' },
          ],
        ],
      },
      {
        expected: expectedTextNotApplicable,
        combinations: [
          [], // This case should not happen in real life, but to make it complete we also expect n/a here.
          [
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
          ],
          [
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            { is_for_current_user: false, progress_status: 'COMPLETE' },
          ],
        ],
      },
      {
        expected: expectedTextNotSubmitted,
        combinations: [
          [
            {
              is_for_current_user: true,
              progress_status: 'NOT_SUBMITTED',
            },
          ],
          [
            {
              is_for_current_user: true,
              progress_status: 'PROGRESS_NOT_APPLICABLE',
            },
            {
              is_for_current_user: true,
              progress_status: 'NOT_SUBMITTED',
            },
          ],
        ],
      },
    ];

    dataProvider.forEach(dataSet => {
      dataSet.combinations.forEach(participantInstances => {
        let actualText = wrapper.vm.getYourProgressText(participantInstances);
        expect(actualText).toBe(dataSet.expected);
      });
    });
  });
});
