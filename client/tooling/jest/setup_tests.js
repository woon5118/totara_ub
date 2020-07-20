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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module tui
 */

import { config } from '@vue/test-utils';

jest.mock('tui/internal/storage');
jest.mock('tui/internal/lang_string_store');
jest.mock('tui/flex_icons');
jest.mock('tui/config');
jest.mock('tui/apollo_client', () => null);
jest.mock('tui/tui');
jest.mock('tui/pending');
jest.mock('tui/i18n');

config.mocks.$str = (key, comp, a) =>
  a ? `[[${key}, ${comp}, ${JSON.stringify(a)}]]` : `[[${key}, ${comp}]]`;

config.mocks.uid = 'id';
config.mocks.$id = x => (x ? 'id-' + x : 'id');
