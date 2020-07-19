/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
 */

import { ApolloClient } from 'apollo-client';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloLink } from 'apollo-link';
import { createHttpLink } from 'apollo-link-http';
import { BatchHttpLink } from 'apollo-link-batch-http';
import { createTuiContextLink } from './internal/apollo/tui_context_link';
import { createDevLink } from './internal/apollo/dev_link';
import { config } from './config';

const httpLinkOptions = {
  uri: config.wwwroot + '/totara/webapi/ajax.php',
  credentials: 'same-origin',
  headers: {
    'X-Totara-Sesskey': config.sesskey,
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

// monkey patch .mutate() to add automatic refetch option
const originalMutate = apolloClient.queryManager.mutate;
apolloClient.queryManager.mutate = function(options) {
  return originalMutate
    .apply(apolloClient.queryManager, arguments)
    .then(result => {
      if (options.refetchAll) {
        apolloClient.reFetchObservableQueries();
      }
      return result;
    });
};

export default apolloClient;
