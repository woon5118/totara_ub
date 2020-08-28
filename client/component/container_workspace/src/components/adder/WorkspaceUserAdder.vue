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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module container_workspace
-->
<!--
  Ideally there should be a core user adder to be used. However, at this point in time, the requirement for
  core user adder is not quite clear yet - hence this component exists. If there is a new user adder component,
  we can just wrap this component around the core adder component - but with our own user's fetching query and still be
  able to work backward compat.
-->
<template>
  <Adder
    :open="open"
    :title="$str('add_members_to_space', 'container_workspace')"
    :loading="$apollo.loading"
    :show-load-more="users.cursor.next"
    class="tui-workspaceUserAdder"
    @cancel="$emit('cancel')"
    @selected-tab-active="selectedUserIds = $event"
    @load-more="fetchMore"
    @added="addedUsers($event)"
  >
    <FilterBar
      slot="browse-filters"
      :has-top-bar="false"
      :title="$str('filter_users', 'container_workspace')"
    >
      <template v-slot:filters-left="{ stacked }">
        <SearchBox
          :value="searchTerm"
          name="user-search-input"
          :label="$str('filter_users', 'container_workspace')"
          :show-label="false"
          :placeholder="$str('search', 'totara_core')"
          @submit="searchTerm = $event"
        />
      </template>
    </FilterBar>

    <template v-slot:browse-list="{ disabledItems, selectedItems, update }">
      <SelectTable
        :large-check-box="true"
        :no-label-offset="true"
        :value="selectedItems"
        :disabled-ids="disabledItems"
        checkbox-v-align="center"
        :select-all-enabled="true"
        :border-bottom-hidden="true"
        :data="users.items || []"
        class="tui-workspaceUserAdder__table"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="12" valign="start">
            {{ $str('name', 'moodle') }}
          </HeaderCell>

          <HeaderCell
            v-for="({ position, label }, index) in fields"
            :key="index"
            :data-field-position="position"
            size="4"
          >
            {{ label }}
          </HeaderCell>
        </template>

        <template v-slot:row="{ row: { card_display, fullname } }">
          <Cell
            size="10"
            :column-header="$str('name', 'moodle')"
            valign="start"
          >
            <div class="tui-workspaceUserAdder__table__name">
              <Avatar
                v-if="card_display.profile_picture_url"
                :src="card_display.profile_picture_url"
                :alt="card_display.profile_picture_alt || fullname"
                size="xsmall"
                class="tui-workspaceUserAdder__table__name__avatar"
              />
              <p class="tui-workspaceUserAdder__table__name__text">
                {{ fullname }}
              </p>
            </div>
          </Cell>

          <template v-for="({ position }, index) in fields">
            <Cell :key="index" size="4" :data-field-position="position">
              {{ card_display.display_fields[position].value || '' }}
            </Cell>
          </template>
        </template>
      </SelectTable>
    </template>

    <template v-slot:basket-list="{ selectedItems, update, disabledItems }">
      <SelectTable
        :large-check-box="true"
        :no-label-offset="true"
        :value="selectedItems"
        :disabled-ids="disabledItems"
        checkbox-v-align="center"
        :select-all-enabled="true"
        :border-bottom-hidden="true"
        :data="selectedUsers"
        class="tui-workspaceUserAdder__Table"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="12" valign="start">
            {{ $str('name', 'moodle') }}
          </HeaderCell>

          <HeaderCell
            v-for="({ position, label }, index) in fields"
            :key="index"
            :data-field-position="position"
            size="4"
          >
            {{ label }}
          </HeaderCell>
        </template>
        <template v-slot:row="{ row: { card_display, fullname } }">
          <Cell
            size="10"
            :column-header="$str('name', 'moodle')"
            valign="start"
          >
            <div class="tui-workspaceUserAdder__table__name">
              <Avatar
                v-if="card_display.profile_picture_url"
                :src="card_display.profile_picture_url"
                :alt="card_display.profile_picture_alt || fullname"
                size="xsmall"
                class="tui-workspaceUserAdder__table__name__avatar"
              />
              <p class="tui-workspaceUserAdder__table__name__text">
                {{ fullname }}
              </p>
            </div>
          </Cell>

          <template v-for="({ position }, index) in fields">
            <Cell :key="index" size="4" :data-field-position="position">
              {{ card_display.display_fields[position].value || '' }}
            </Cell>
          </template>
        </template>
      </SelectTable>
    </template>
  </Adder>
</template>

<script>
import Adder from 'tui/components/adder/Adder';
import FilterBar from 'tui/components/filters/FilterBar';
import SearchBox from 'tui/components/form/SearchBox';
import SelectTable from 'tui/components/datatable/SelectTable';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Cell from 'tui/components/datatable/Cell';
import Avatar from 'tui/components/avatar/Avatar';

// GraphQL queries
import nonMemberUsers from 'container_workspace/graphql/non_member_users';
import userTableFields from 'container_workspace/graphql/user_table_fields';

export default {
  components: {
    Avatar,
    Adder,
    FilterBar,
    SearchBox,
    SelectTable,
    HeaderCell,
    Cell,
  },

  props: {
    workspaceId: {
      type: [String, Number],
      required: true,
    },
    open: Boolean,
  },

  apollo: {
    fields: userTableFields,
    users: {
      query: nonMemberUsers,
      fetchPolicy: 'network-only',
      variables() {
        return {
          workspace_id: this.workspaceId,
          search_term: this.searchTerm,
        };
      },

      update({ cursor, users }) {
        return {
          cursor,
          items: users,
        };
      },
    },
  },

  data() {
    return {
      fields: [],
      users: {
        items: [],
        cursor: {
          total: 0,
          next: null,
        },
      },
      searchTerm: '',
      selectedUserIds: [],
    };
  },

  computed: {
    selectedUsers() {
      return this.users.items.filter(({ id }) => {
        return Array.prototype.includes.call(this.selectedUserIds, id);
      });
    },
  },

  beforeDestroy() {
    this.selectedUserIds = [];
  },

  methods: {
    async fetchMore() {
      this.$apollo.queries.users.fetchMore({
        variables: {
          cursor: this.users.cursor.next,
        },

        updateQuery(
          { users: oldUsers },
          { fetchMoreResult: { users, cursor } }
        ) {
          return {
            cursor: cursor,
            users: oldUsers.concat(users),
          };
        },
      });
    },

    addedUsers(event) {
      this.$emit('add-members', event);
      this.$apollo.queries.users.refetch();
    },
  },
};
</script>

<lang-strings>
{
  "container_workspace": [
    "add_members_to_space",
    "filter_users"
  ],

  "totara_core": [
    "search"
  ],

  "moodle": [
    "name"
  ]
}
</lang-strings>

<style lang="scss">
.tui-workspaceUserAdder {
  &__table {
    &__name {
      display: flex;
      // IE support
      flex-grow: 1;
      align-items: center;

      &__text {
        @include tui-font-body();
        margin: 0;
        margin-left: var(--gap-2);
      }
    }
  }
}
</style>
