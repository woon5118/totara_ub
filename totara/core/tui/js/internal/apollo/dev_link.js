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

import { ApolloLink, Observable } from 'apollo-link';
import { url } from '../../util';

let executeDevQuery;

export function createDevLink() {
  return new ApolloLink((operation, forward) => {
    const { operationName } = operation;
    // avoid breaking Apollo DevTools
    // also send queries without names to dev executor
    if (
      process.env.NODE_ENV == 'development' &&
      (operationName == 'IntrospectionQuery' || operationName == null)
    ) {
      return executeDevQuery(operation);
    }

    return forward(operation);
  });
}

if (process.env.NODE_ENV == 'development') {
  // use require rather than import so it can be removed in optimization pass
  const { print } = require('graphql/language/printer');
  executeDevQuery = function({ operationName, variables, query }) {
    const displayName = operationName
      ? `query "${operationName}"`
      : 'unnamed query';

    // warn so we don't accidentally ship code using unnamed queries
    if (!operationName) {
      console.warn(
        `[webapi-link] Running unnamed query using dev GraphQL executor.\n` +
          `This query must be converted to a named query for production use.` +
          (query.loc && query.loc.source ? '\n\n' + query.loc.source.body : '')
      );
    }

    return new Observable(async observer => {
      try {
        const response = await fetch(
          url('/totara/webapi/dev_graphql_executor.php'),
          {
            credentials: 'same-origin',
            method: 'post',
            headers: {
              accept: '*/*',
              'content-type': 'application/json',
            },
            body: JSON.stringify({
              operationName,
              variables,
              query: print(query),
            }),
          }
        );
        const result = await response.json();
        observer.next(result);
      } catch (err) {
        console.warn(
          `[webapi-link] Failed to run ${displayName} using dev GraphQL executor. ` +
            (operationName == 'IntrospectionQuery'
              ? 'Schema information will not be available in Apollo DevTools. '
              : '') +
            'Please make sure GRAPHQL_DEVELOPMENT_MODE is defined in config.php.'
        );

        if (operationName == 'IntrospectionQuery') {
          // Apollo DevTools crashes if we report the error, so let's just pretend we got a response
          observer.next({});
        } else {
          observer.error(err);
        }
      }
      observer.complete();
    });
  };
}
