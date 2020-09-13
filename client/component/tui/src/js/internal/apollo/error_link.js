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
 * @author Arshad Anwer <arshad.anwer@totaralearning.com>
 * @module tui
 */

import { totaraUrl } from '../../util';
import { onError } from 'apollo-link-error';

export const createErrorLink = () => {
  return onError(({ graphQLErrors, networkError }) => {
    if (graphQLErrors) {
      const loginCategory = graphQLErrors.find(
        x => x.extensions && x.extensions.category === 'require_login'
      );
      if (loginCategory) {
        window.location = totaraUrl('/login/index.php');
      }
    }

    if (
      networkError &&
      networkError.result &&
      Array.isArray(networkError.result.errors)
    ) {
      const loginCategory = networkError.result.errors.find(
        x => x.extensions && x.extensions.category === 'require_login'
      );
      if (loginCategory) {
        window.location = totaraUrl('/login/index.php');
      }
    }
  });
};
