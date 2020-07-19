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

import { ApolloClient } from 'apollo-client';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloLink } from 'apollo-link';
import { createHttpLink } from 'apollo-link-http';
import { BatchHttpLink } from 'apollo-link-batch-http';
import { createTuiContextLink } from './internal/apollo/tui_context_link';
import { createDevLink } from './internal/apollo/dev_link';
import { globalConfig } from './config';

const httpLinkOptions = {
  uri: globalConfig.wwwroot + '/totara/webapi/ajax.php',
  credentials: 'same-origin',
  headers: {
    'X-Totara-Sesskey': globalConfig.sesskey,
  },
};

const link = ApolloLink.from([
  createTuiContextLink(),
  createDevLink(),
  ApolloLink.split(
    operation => operation.getContext().batch,
    new BatchHttpLink(httpLinkOptions),
    createHttpLink(httpLinkOptions)
  ),
]);

const cache = new InMemoryCache({
  addTypename: false,
  freezeResults: true,
});

const apolloClient = new ApolloClient({
  link,
  cache,
  assumeImmutableResults: true,
  resolvers: {},
});

// monkey patch .mutate() to automatically refetch
const originalMutate = apolloClient.queryManager.mutate;
apolloClient.queryManager.mutate = function(options) {
  return originalMutate
    .apply(apolloClient.queryManager, arguments)
    .then(result => {
      if (options.refetchAll !== false) {
        apolloClient.reFetchObservableQueries();
      }
      return result;
    });
};

export default apolloClient;
