<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package pathway_manual
-->

<template>
  <div v-if="isLoaded" class="tui-pathwayManual-rateUsersList">
    <SearchBox
      class="tui-pathwayManual-rateUsersList__search"
      :placeholder="$str('search_people', 'pathway_manual')"
      @input="name => (search = name)"
      @submit="searchPeople"
    />
    <div class="tui-pathwayManual-rateUsersList__userCount">
      {{ $str('number_of_people', 'pathway_manual', users.length) }}
    </div>
    <Table v-if="hasUsers" :data="users" :expandable-rows="false">
      <template v-slot:header-row>
        <HeaderCell size="5">
          {{ columnHeaders[0] }}
        </HeaderCell>
        <HeaderCell size="3">
          {{ columnHeaders[1] }}
        </HeaderCell>
        <HeaderCell size="4">
          <span style="display: inline">
            {{ columnHeaders[2] }}
            <LastRatingHelp />
          </span>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row }">
        <Cell size="5" valign="center">
          <Avatar
            :src="row.user.profileimageurl"
            :alt="row.user.fullname"
            size="small"
            class="tui-pathwayManual-rateUsersList__avatar"
          />
          <a :href="getRatingUrl(row.user.id)">{{ row.user.fullname }}</a>
        </Cell>
        <Cell size="3" :column-header="columnHeaders[1]" valign="center">
          {{ row.competency_count }}
        </Cell>
        <Cell size="4" :column-header="columnHeaders[2]" valign="center">
          <LastRatingBlock
            :show-value="false"
            :latest-rating="row.latest_rating"
            :current-user-id="currentUserId"
          />
        </Cell>
      </template>
    </Table>
    <div v-else>
      <em>{{ $str('filter:no_users', 'pathway_manual') }}</em>
    </div>
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import LastRatingBlock from 'pathway_manual/components/LastRatingBlock';
import LastRatingHelp from 'pathway_manual/components/LastRatingHelp';
import SearchBox from 'totara_core/components/form/SearchBox';
import Table from 'totara_core/components/datatable/Table';

import RateableUsersQuery from '../../webapi/ajax/rateable_users.graphql';

export default {
  components: {
    Avatar,
    Cell,
    HeaderCell,
    LastRatingBlock,
    LastRatingHelp,
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

  computed: {
    columnHeaders() {
      return [
        this.$str('name'),
        this.$str('competencies', 'totara_hierarchy'),
        this.$str('last_rated', 'pathway_manual'),
      ];
    },

    isLoaded() {
      return !this.$apollo.loading || this.search.length > 0;
    },

    hasUsers() {
      return this.users.length > 0;
    },
  },

  methods: {
    searchPeople() {
      this.filters = {
        user_full_name: this.search.length > 0 ? this.search : null,
      };
    },

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

<style lang="scss">
.tui-pathwayManual-rateUsersList {
  display: block;

  &__avatar {
    margin-right: var(--tui-gap-1);
  }

  &__userCount {
    @include tui-font-heading-x-small;
    width: 100%;
    margin-bottom: var(--tui-gap-4);
    padding: var(--tui-gap-1) var(--tui-gap-2);
    background-color: var(--tui-color-neutral-4);
    border-top: var(--tui-font-size-1) solid var(--tui-color-neutral-6);
  }

  &__search {
    max-width: $tui-screen-xs;
    margin-top: var(--tui-gap-4);
    margin-bottom: var(--tui-gap-4);
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "name"
    ],
    "pathway_manual": [
      "filter:no_users",
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
