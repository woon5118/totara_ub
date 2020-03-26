<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @package totara_webapi
-->

<template>
  <div>
    <p>
      This is a test page to test queries using sessions and queries using
      nosession.
    </p>
    <div>
      <button type="button" @click="sendSessionQuery()">
        {{ sessionButtonText }}
      </button>
      <h4 v-if="session">Result:</h4>
      <div v-if="session">
        <pre>{{ session }}</pre>
      </div>
      <h4 v-if="sessionError">Error:</h4>
      <div v-if="sessionError">
        <pre>{{ sessionError }}</pre>
      </div>
    </div>
    <div><hr /></div>
    <div>
      <button type="button" @click="sendNosessionQuery()">
        {{ nosessionButtonText }}
      </button>
      <h3 v-if="nosession">Result:</h3>
      <div v-if="nosession">
        <pre>{{ nosession }}</pre>
      </div>
      <h4 v-if="nosessionError">Error:</h4>
      <div v-if="nosessionError">
        <pre>{{ nosessionError }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
import statusNosessionQuery from 'totara_webapi/graphql/status_nosession';
import statusSessionQuery from 'totara_webapi/graphql/status';

export default {
  components: {},
  props: {},
  data() {
    return {
      sessionButtonText: 'Send session query',
      nosessionButtonText: 'Send nosession query',
      nosession: '',
      session: '',
      sessionError: '',
      nosessionError: '',
    };
  },
  methods: {
    sendSessionQuery() {
      this.$apollo.queries.session.skip = false;
      this.$apollo.queries.session.refetch();
    },

    sendNosessionQuery() {
      this.$apollo.queries.nosession.skip = false;
      this.$apollo.queries.nosession.refetch();
    },
  },
  apollo: {
    nosession: {
      query: statusNosessionQuery,
      update: data => data.totara_webapi_status,
      error(error) {
        this.nosessionError = error.networkError.result.errors;
      },
      skip: true,
      fetchPolicy: 'no-cache',
    },
    session: {
      query: statusSessionQuery,
      update: data => data.totara_webapi_status,
      error: function(error) {
        this.sessionError = error.networkError.result.errors;
      },
      skip: true,
      fetchPolicy: 'no-cache',
    },
  },
};
</script>
