/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

import { ApolloLink } from 'apollo-link';
import pending from '../../pending';

/**
 * Manipulate query context as needed for server-side support
 */
export function createTuiContextLink() {
  return new ApolloLink((operation, forward) => {
    const context = operation.getContext();
    let headers = context.headers;

    // set nosession flag if needed
    // this isn't really used at the moment, but apollo-link-batch-http batches
    // operations with different headers separately
    if (
      operation.operationName &&
      operation.operationName.endsWith('_nosession')
    ) {
      headers = Object.assign({}, headers, { 'X-Totara-Nosession': '1' });
    }

    operation.setContext({
      headers,
      http: {
        includeExtensions: true,
        // don't need the query, only the operation name
        includeQuery: false,
      },
    });

    // track pending request
    const done = pending('apollo-request');
    return forward(operation).map(data => {
      done();
      return data;
    });
  });
}
