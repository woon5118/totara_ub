<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module container_workspace
-->
<template>
  <div class="tui-workspaceMembersTab">
    <WorkspaceMemberRequestList
      v-if="canViewMemberRequests && totalMemberRequests > 0"
      :workspace-id="workspaceId"
      class="tui-workspaceMembersTab__list"
    />

    <div class="tui-workspaceMembersTab__head">
      <MemberSearchBox
        :search-term="searchTerm"
        @submit="searchTerm = $event"
      />
    </div>

    <div
      v-if="!$apollo.queries.filter.loading"
      class="tui-workspaceMembersTab__content"
    >
      <div class="tui-workspaceMembersTab__content__head">
        <h3 class="tui-workspaceMembersTab__content__head__title">
          <span>{{ totalMemberText }}</span>
        </h3>

        <SelectFilter
          v-model="sort"
          :options="filter.sorts"
          :label="$str('sortby', 'moodle')"
          :aria-label="$str('sortby', 'moodle')"
        />
      </div>

      <div class="tui-workspaceMembersTab__content__members">
        <VirtualScroll
          data-key="id"
          :data-list="member.items"
          :aria-label="$str('member_list', 'container_workspace')"
          :is-loading="$apollo.queries.member.loading"
          :page-mode="true"
          @scrollbottom="onScrollToBottom"
        >
          <template v-slot:item="{ item, posInSet, setSize }">
            <WorkspaceMemberCard
              :user-id="item.user.id"
              :user-full-name="item.user.fullname"
              :user-card-display="item.user.card_display"
              :delete-able="item.member_interactor.can_remove"
              :workspace-id="workspaceId"
              class="tui-workspaceMembersTab__content__members__member"
              :aria-posinset="posInSet"
              :aria-setsize="setSize"
              :aria-labelledby="$id(`item-${item.id}`)"
              :label-id="$id(`item-${item.id}`)"
              @remove-member="removeMember"
            />
          </template>
          <template v-slot:footer>
            <PageLoader
              :fullpage="false"
              :loading="$apollo.queries.member.loading"
            />
          </template>
        </VirtualScroll>
        <div
          v-if="
            isLoadMoreVisible &&
              member.cursor.next &&
              !$apollo.queries.member.loading
          "
          class="tui-workspaceMembersTab__content__members__loadMoreContainer"
        >
          <div class="tui-workspaceMembersTab__content__members__viewedMembers">
            <template>
              {{
                $str('vieweditems', 'container_workspace', member.items.length)
              }}
              {{ totalMemberText }}
            </template>
          </div>
          <Button
            class="tui-workspaceMembersTab__content__members__loadMore"
            :text="$str('loadmore', 'container_workspace')"
            @click="loadMore"
          />
        </div>

        <p
          v-if="member.items.length <= 0 && !$apollo.queries.member.loading"
          class="tui-workspaceMembersTab__content__members__message"
        >
          {{ $str('no_member_found', 'container_workspace') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import MemberSearchBox from 'container_workspace/components/filter/MemberSearchBox';
import WorkspaceMemberCard from 'container_workspace/components/card/WorkspaceMemberCard';
import SelectFilter from 'tui/components/filters/SelectFilter';
import apolloClient from 'tui/apollo_client';
import VirtualScroll from 'tui/components/virtualscroll/VirtualScroll';
import Button from 'tui/components/buttons/Button';
import PageLoader from 'tui/components/loader/Loader';
import WorkspaceMemberRequestList from 'container_workspace/components/content/WorkspaceMemberRequestList';

// GraphQL queries
import getMemberFilterOptions from 'container_workspace/graphql/member_filter_options';
import findMembers from 'container_workspace/graphql/find_members';

export default {
  components: {
    MemberSearchBox,
    WorkspaceMemberCard,
    SelectFilter,
    VirtualScroll,
    Button,
    PageLoader,
    WorkspaceMemberRequestList,
  },

  props: {
    workspaceId: {
      type: [String, Number],
      required: true,
    },

    selectedSort: {
      type: String,
      required: true,
    },

    totalMembers: {
      type: [String, Number],
      required: true,
    },

    totalMemberRequests: {
      type: [String, Number],
      default: 0,
    },

    canViewMemberRequests: Boolean,
  },

  apollo: {
    filter: {
      query: getMemberFilterOptions,
      update({ sorts }) {
        return {
          sorts: Array.prototype.map.call(sorts, ({ label, value }) => {
            return {
              id: value,
              label: label,
            };
          }),
        };
      },
    },

    member: {
      query: findMembers,
      fetchPolicy: 'network-only',
      skip() {
        return this.$apollo.queries.filter.loading;
      },
      variables() {
        return {
          workspace_id: this.workspaceId,
          sort: this.sort,
          search_term: this.searchTerm,
        };
      },

      update({ cursor, members }) {
        return {
          cursor: cursor,
          items: members,
        };
      },
    },
  },

  data() {
    return {
      filter: {},
      isLoadMoreVisible: false,
      member: {
        cursor: {
          total: 0,
          next: null,
        },
        items: [],
      },
      searchTerm: '',
      sort: this.selectedSort,
    };
  },

  computed: {
    totalMemberText() {
      if (1 == this.totalMembers) {
        return this.$str('one_member', 'container_workspace');
      }

      return this.$str(
        'total_member_x',
        'container_workspace',
        this.totalMembers
      );
    },
  },

  methods: {
    /**
     *
     * @param {Number|String} userId
     */
    removeMember(userId) {
      const variables = {
        search_term: this.searchTerm,
        sort: this.sort,
        workspace_id: this.workspaceId,
      };

      let { cursor, members } = apolloClient.readQuery({
        query: findMembers,
        variables: variables,
      });

      apolloClient.writeQuery({
        query: findMembers,
        variables: variables,
        data: {
          cursor: cursor,
          members: Array.prototype.filter.call(members, ({ user: { id } }) => {
            return id != userId;
          }),
        },
      });
    },

    async onScrollToBottom() {
      if (this.isLoadMoreVisible) {
        return;
      }
      await this.loadMoreItems();
      this.isLoadMoreVisible = true;
    },

    async loadMore() {
      await this.loadMoreItems();
      this.isLoadMoreVisible = false;
    },

    async loadMoreItems() {
      if (!this.member.cursor.next) {
        return;
      }
      this.$apollo.queries.member.fetchMore({
        variables: {
          cursor: this.member.cursor.next,
          workspace_id: this.workspaceId,
          sort: this.sort,
          search_term: this.searchTerm,
        },
        updateQuery: (previousResult, { fetchMoreResult }) => {
          const oldData = previousResult;
          const newData = fetchMoreResult;
          const newList = oldData.members.concat(newData.members);

          return {
            cursor: newData.cursor,
            members: newList,
          };
        },
      });
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "loadmore",
      "one_member",
      "total_member_x",
      "no_member_found",
      "vieweditems",
      "member_list"
    ],

    "moodle": [
      "sortby"
    ]
  }
</lang-strings>
