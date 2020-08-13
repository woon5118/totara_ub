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
  <div class="tui-workspaceDiscussionTab">
    <template v-if="canPostDiscussion">
      <PostDiscussionForm
        :avatar-image-alt="user.profileimagealt || user.fullname"
        :avatar-image-src="user.profileimageurl"
        :submitting="submitting"
        :avatar-image-url="$url('/user/profile', { id: user.id })"
        @submit="submit"
      />

      <Separator />
    </template>

    <DiscussionFilter
      v-if="workspaceTotalDiscussions"
      :sort="sort"
      :search-term="searchTerm"
      :workspace-id="workspaceId"
      class="tui-workspaceDiscussionTab__filter"
      @update-search-term="searchTerm = $event"
      @update-sort="sort = $event"
    />

    <!-- Using the discussion's id so that we can make sure the state is being reset after ward. -->
    <VirtualScroll
      data-key="id"
      :data-list="page.discussions"
      :aria-label="$str('discussions_list', 'container_workspace')"
      :is-loading="$apollo.loading"
      :page-mode="true"
      @scrollbottom="onScrollToBottom"
    >
      <template v-slot:item="{ item, posInSet, setSize }">
        <DiscussionCard
          :creator-fullname="item.owner.fullname"
          :creator-image-alt="item.owner.profileimagealt || item.owner.fullname"
          :creator-image-src="item.owner.profileimageurl"
          :creator-id="item.owner.id"
          :discussion-content="item.content"
          :time-description="item.time_description"
          :total-comments="item.total_comments"
          :total-reactions="item.total_reactions"
          :discussion-id="item.id"
          :first-comment-cursor="item.comment_cursor"
          :reacted="item.discussion_interactor.reacted"
          :update-able="item.discussion_interactor.can_update"
          :delete-able="item.discussion_interactor.can_delete"
          :comment-able="item.discussion_interactor.can_comment"
          :react-able="item.discussion_interactor.can_react"
          :edited="item.edited"
          :aria-posinset="posInSet"
          :aria-setsize="setSize"
          :aria-labelledby="$id(`item-${item.id}`)"
          :label-id="$id(`item-${item.id}`)"
          class="tui-workspaceDiscussionTab__card"
          @update-discussion-react-status="updateDiscussionReactStatus"
          @update-discussion="updateDiscussion"
          @add-new-comment="addNewComment"
          @delete-discussion="deleteDiscussion"
        />
      </template>
      <template v-slot:footer>
        <PageLoader :fullpage="false" :loading="$apollo.loading" />
      </template>
    </VirtualScroll>
    <div
      v-if="loadMoreVisibility"
      class="tui-workspaceDiscussionTab__loadMoreContainer"
    >
      <div class="tui-workspaceDiscussionTab__viewedDiscussions">
        {{
          $str('vieweditems', 'container_workspace', page.discussions.length)
        }}
        {{
          $str('total_discussions', 'container_workspace', page.cursor.total)
        }}
      </div>
      <Button
        class="tui-workspaceDiscussionTab__loadMore"
        :text="$str('loadmore', 'container_workspace')"
        @click="loadMore"
      />
    </div>
  </div>
</template>

<script>
import Separator from 'tui/components/decor/Separator';
import DiscussionCard from 'container_workspace/components/card/DiscussionWithCommentCard';
import DiscussionFilter from 'container_workspace/components/filter/DiscussionFilter';
import apolloClient from 'tui/apollo_client';
import { notify } from 'tui/notifications';
import PostDiscussionForm from 'container_workspace/components/form/PostDiscussionForm';

// GraphQL
import getWorkspaceInteractor from 'container_workspace/graphql/workspace_interactor';
import postDiscussion from 'container_workspace/graphql/post_discussion';
import getDiscussions from 'container_workspace/graphql/get_discussions';
import VirtualScroll from 'tui/components/virtualscroll/VirtualScroll';
import Button from 'tui/components/buttons/Button';
import PageLoader from 'tui/components/loader/Loader';

import VirtualScrollMixin from 'container_workspace/mixins/virtual_scroll_mixin';

export default {
  components: {
    Separator,
    PostDiscussionForm,
    DiscussionCard,
    DiscussionFilter,
    VirtualScroll,
    Button,
    PageLoader,
  },

  mixins: [VirtualScrollMixin],

  props: {
    workspaceId: {
      type: [Number, String],
      required: true,
    },

    selectedSort: {
      type: String,
      required: true,
    },

    /**
     * A total (aggregate) number of discussions within a workspace. This number will be
     * completely different from the number total from `page.cursor`
     */
    workspaceTotalDiscussions: {
      type: [Number, String],
      required: true,
    },
  },

  apollo: {
    interactor: {
      query: getWorkspaceInteractor,
      variables() {
        return {
          workspace_id: this.workspaceId,
        };
      },
    },
    /**
     * Fetching the none-pinned discussions within this workspace.
     */
    page: {
      query: getDiscussions,
      fetchPolicy: 'network-only',
      variables() {
        return {
          workspace_id: this.workspaceId,
          sort: this.sort,
          search_term: this.searchTerm,
        };
      },

      update({ cursor, discussions }) {
        return {
          cursor: cursor,
          discussions: discussions,
        };
      },
    },
  },

  data() {
    return {
      interactor: {},
      submitting: false,
      /**
       * This is for the page's discussions.
       */
      page: {
        cursor: {
          total: 0,
          next: null,
        },
        discussions: [],
      },
      sort: this.selectedSort,
      searchTerm: '',
    };
  },

  computed: {
    /**
     * If the current user in session is already a member of a workspace,
     * then he/she should be able to post the discussion
     * @return {Boolean}
     */
    canPostDiscussion() {
      return this.interactor.joined;
    },

    /**
     * Returning the user object within workspace's interactor object.
     * @return {Object}
     */
    user() {
      if (!this.interactor.user) {
        return {};
      }

      return this.interactor.user;
    },

    /**
     * @return {{
     *   workspace_id: Number,
     *   sort: String,
     *   search_term: String
     * }}
     */
    discussionQueryVariables() {
      return {
        workspace_id: this.workspaceId,
        sort: this.sort,
        search_term: this.searchTerm,
      };
    },

    loadMoreVisibility() {
      return (
        this.isLoadMoreVisible && this.page.cursor.next && !this.$apollo.loading
      );
    },
  },

  watch: {
    /**
     * @param {String} value
     */
    selectedSort(value) {
      this.sort = value;
    },
  },

  methods: {
    /**
     * @return {{
     *   cursor: Object,
     *   discussions: Array
     * }}
     */
    $_getDiscussionCache() {
      return apolloClient.readQuery({
        query: getDiscussions,
        variables: this.discussionQueryVariables,
      });
    },

    /**
     *
     * @param {String} content
     * @param {Number} itemId
     */
    async submit({ content, itemId }) {
      if (this.submitting) {
        return;
      }

      this.submitting = true;

      try {
        await this.$apollo.mutate({
          mutation: postDiscussion,
          refetchAll: false,
          variables: {
            workspace_id: this.workspaceId,
            content: content,
            draft_id: itemId,
          },

          update: (proxy, { data: { discussion } }) => {
            const variables = {
              workspace_id: this.workspaceId,
              sort: this.sort,
              search_term: this.searchTerm,
            };

            let { cursor, discussions } = proxy.readQuery({
              query: getDiscussions,
              variables: variables,
            });

            cursor = Object.assign({}, cursor);
            cursor.total += 1;

            proxy.writeQuery({
              query: getDiscussions,
              variables: variables,
              data: {
                cursor: cursor,
                discussions: Array.prototype.concat.call(
                  [discussion],
                  discussions
                ),
              },
            });
          },
        });

        this.$emit('add-discussion');
      } catch (e) {
        await notify({
          message: this.$str(
            'error:create_discussion',
            'container_workspace',
            this.workspaceId
          ),
          type: 'error',
        });
      } finally {
        this.submitting = false;
      }
    },

    /**
     *
     * @param {Number}  discussionId
     * @param {Boolean} status
     */
    updateDiscussionReactStatus({ discussionId, status }) {
      let { discussions, cursor } = this.$_getDiscussionCache();

      apolloClient.writeQuery({
        query: getDiscussions,
        variables: this.discussionQueryVariables,
        data: {
          cursor: cursor,
          discussions: Array.prototype.map.call(discussions, discussion => {
            if (discussion.id == discussionId) {
              let innerDiscussion = Object.assign({}, discussion),
                interactor = Object.assign(
                  {},
                  innerDiscussion.discussion_interactor
                );

              interactor.reacted = status;
              innerDiscussion.discussion_interactor = interactor;

              if (status) {
                innerDiscussion.total_reactions += 1;
              } else if (0 != innerDiscussion.total_reactions) {
                innerDiscussion.total_reactions -= 1;
              }

              return innerDiscussion;
            }

            return discussion;
          }),
        },
      });
    },

    /**
     *
     * @param {Object} discussion
     */
    updateDiscussion(discussion) {
      let { discussions, cursor } = this.$_getDiscussionCache();
      apolloClient.writeQuery({
        query: getDiscussions,
        variables: this.discussionQueryVariables,
        data: {
          cursor: cursor,
          discussions: Array.prototype.map.call(
            discussions,
            cacheDiscussion => {
              if (cacheDiscussion.id === discussion.id) {
                return discussion;
              }

              return cacheDiscussion;
            }
          ),
        },
      });
    },

    /**
     *
     * @param {Number} discussionId
     */
    addNewComment(discussionId) {
      let { discussions, cursor } = this.$_getDiscussionCache();
      apolloClient.writeQuery({
        query: getDiscussions,
        variables: this.discussionQueryVariables,
        data: {
          cursor: cursor,
          discussions: Array.prototype.map.call(discussions, discussion => {
            if (discussion.id == discussionId) {
              let newDiscussion = Object.assign({}, discussion);
              newDiscussion.total_comments += 1;

              return newDiscussion;
            }

            return discussion;
          }),
        },
      });
    },

    async loadMoreItems() {
      if (!this.page.cursor.next) {
        return;
      }
      this.$apollo.queries.page.fetchMore({
        variables: {
          cursor: this.page.cursor.next,
          workspace_id: this.workspaceId,
          sort: this.sort,
          search_term: this.searchTerm,
        },
        updateQuery: (previousResult, { fetchMoreResult }) => {
          const oldData = previousResult;
          const newData = fetchMoreResult;
          const newList = oldData.discussions.concat(newData.discussions);

          return {
            cursor: newData.cursor,
            discussions: newList,
          };
        },
      });
    },

    deleteDiscussion(discussionId) {
      let { discussions, cursor } = this.$_getDiscussionCache();
      apolloClient.writeQuery({
        query: getDiscussions,
        variables: this.discussionQueryVariables,
        data: {
          cursor: cursor,
          discussions: Array.prototype.filter.call(discussions, ({ id }) => {
            return id != discussionId;
          }),
        },
      });
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "error:create_discussion",
      "loadmore",
      "total_discussions",
      "vieweditems",
      "discussions_list"
    ]
  }
</lang-strings>
