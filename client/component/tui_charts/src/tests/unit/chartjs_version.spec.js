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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module tui_charts
 */

const packagejson = require('../../../../../../package.json');

test('Chart.js is still at 2.9.3', () => {
  // TL-30829: Competency profile chart (IndividualAssignmentProgress) is not
  // compatible with Chart.js 2.9.4.
  // Ensure it doesn't get updated by accident until we are able to resolve the
  // issue with the competency profile.
  expect(packagejson.dependencies['chart.js']).toBe('2.9.3');
});
