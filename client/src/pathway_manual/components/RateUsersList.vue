<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module pathway_manual
-->

<template>
  <div class="tui-bulkManualRatingRateUsersList">
    <div class="tui-bulkManualRatingRateUsersList__search">
      <SearchBox
        :placeholder="$str('search_people', 'pathway_manual')"
        @input="name => (search = name)"
        @submit="searchPeople"
      />
    </div>
    <Loader :loading="$apollo.loading">
      <h4>
        {{ $str('number_of_people', 'pathway_manual', users.length) }}
      </h4>
      <Table v-if="users.length > 0" :data="users" :expandable-rows="false">
        <template v-slot:header-row>
          <HeaderCell size="5">
            {{ $str('name') }}
          </HeaderCell>
          <HeaderCell size="3">
            {{ $str('competencies', 'totara_hierarchy') }}
          </HeaderCell>
          <HeaderCell size="4">
            <div class="tui-bulkManualRatingRateUsersList__flexRow">
              {{ $str('last_rated', 'pathway_manual') }}
              <span class="tui-bulkManualRatingRateUsersList__help">
                <LastRatingHelp />
              </span>
            </div>
          </HeaderCell>
        </template>
        <template v-slot:row="{ row }">
          <Cell size="5" valign="center">
            <Avatar
              :src="row.user.profileimageurl"
              :alt="row.user.fullname"
              size="small"
              class="tui-bulkManualRatingRateUsersList__avatar"
            />
            <a :href="getRatingUrl(row.user.id)">{{ row.user.fullname }}</a>
          </Cell>
          <Cell
            size="3"
            :column-header="$str('competencies', 'totara_hierarchy')"
            valign="center"
          >
            {{ row.competency_count }}
          </Cell>
          <Cell
            size="4"
            :column-header="$str('last_rated', 'pathway_manual')"
            valign="center"
          >
            <LastRatingBlock
              :show-value="false"
              :latest-rating="row.latest_rating"
              :current-user-id="currentUserId"
            />
          </Cell>
        </template>
      </Table>
      <div v-else class="tui-bulkManualRatingRateUsersList__noUsers">
        {{ $str('filter_no_users', 'pathway_manual') }}
      </div>
    </Loader>
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import LastRatingBlock from 'pathway_manual/components/LastRatingBlock';
import LastRatingHelp from 'pathway_manual/components/LastRatingHelp';
import Loader from 'totara_core/components/loader/Loader';
import RateableUsersQuery from 'pathway_manual/graphql/rateable_users.graphql';
import SearchBox from 'totara_core/components/form/SearchBox';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Avatar,
    Cell,
    HeaderCell,
    LastRatingBlock,
    LastRatingHelp,
    Loader,
    SearchBox,
    Table,
  },

  props: {
    role: {
      required: true,
      type: String,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      users: [],
      filters: {
        user_full_name: null,
      },
      search: '',
    };
  },

  methods: {
    /**
     * Apply the user fullname filter.
     */
    searchPeople() {
      this.filters = {
        user_full_name: this.search.length > 0 ? this.search : null,
      };
    },

    /**
     * Get the URL for rating an individual user.
     * @param userId The user.
     * @returns {string}
     */
    getRatingUrl(userId) {
      return this.$url('/totara/competency/rate_competencies.php', {
        user_id: userId,
        role: this.role,
      });
    },
  },

  apollo: {
    users: {
      query: RateableUsersQuery,
      variables() {
        return {
          role: this.role,
          filters: this.filters,
        };
      },
      update({ pathway_manual_rateable_users: users }) {
        return users;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "name"
    ],
    "pathway_manual": [
      "filter_no_users",
      "last_rated",
      "number_of_people",
      "rate_user",
      "search_people"
    ],
    "totara_hierarchy": [
      "competencies"
    ]
  }
</lang-strings>
