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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_perform
-->

<template>
  <div class="tui-performManageActivityList">
    <PageHeading :title="$str('perform:manage_activity', 'mod_perform')" />

    <div class="tui-performManageActivityList__wrapper">
      <div
        v-if="paginatedActivities.total"
        class="tui-performManageActivityList__total"
      >
        {{
          $str('showing_activities', 'mod_perform', {
            number: paginatedActivities.items.length,
            total: paginatedActivities.total,
          })
        }}
      </div>
      <div class="tui-performManageActivityList__add">
        <Button
          v-if="canAdd"
          :styleclass="{ primary: true }"
          :disabled="$apollo.loading"
          :text="$str('add_activity', 'mod_perform')"
          @click="showCreateModal()"
        />
      </div>
    </div>
    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <CreateActivityForm
        @mutation-success="activityCreated"
        @mutation-error="creationError"
      />
    </ModalPresenter>

    <loader :loading="$apollo.loading">
      <Table
        :data="paginatedActivities.items"
        class="tui-performManageActivityList__table"
      >
        <template v-slot:header-row>
          <HeaderCell size="9">
            {{ $str('view_name', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell size="2">
            {{ $str('view_type', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell size="2">
            {{ $str('view_status', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell size="1" />
        </template>
        <template v-slot:row="{ row }">
          <Cell size="9" :column-header="$str('view_name', 'mod_perform')">
            <a v-if="row.can_manage" :href="getEditActivityUrl(row.id)">{{
              row.name
            }}</a>
            <div v-else>{{ row.name }}</div>
          </Cell>
          <Cell size="2" :column-header="$str('view_type', 'mod_perform')">
            {{ row.type.display_name }}
          </Cell>
          <Cell size="2" :column-header="$str('view_status', 'mod_perform')">
            {{ row.state_details.display_name }}
          </Cell>
          <Cell size="1" :column-header="$str('view_actions', 'mod_perform')">
            <ActivityActions
              :activity="row"
              @refetch="refetchActivities"
              @activity-cloned="activityCloned"
            />
          </Cell>
        </template>
      </Table>
      <div
        v-if="!$apollo.loading && hasMoreActivities"
        class="tui-performManageActivityList__loadMore"
      >
        <Button :text="$str('loadmore', 'totara_core')" @click="loadMore" />
      </div>
    </loader>
  </div>
</template>

<script>
import ActivityActions from 'mod_perform/components/activities_list/ActivityActions';
import Button from 'tui/components/buttons/Button';
import Cell from 'tui/components/datatable/Cell';
import CreateActivityForm from 'mod_perform/components/manage_activity/CreateActivityForm';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Loader from 'tui/components/loading/Loader';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import PageHeading from 'tui/components/layouts/PageHeading';
import Table from 'tui/components/datatable/Table';
import { notify } from 'tui/notifications';
import { redirectWithPost } from 'mod_perform/redirect';
import { DEFAULT_ITEMS_PER_PAGE } from 'mod_perform/constants';
// Query
import performActivitiesQuery from 'mod_perform/graphql/paginated_activities';

export default {
  components: {
    ActivityActions,
    Button,
    Cell,
    CreateActivityForm,
    HeaderCell,
    Loader,
    ModalPresenter,
    PageHeading,
    Table,
  },
  props: {
    editUrl: {
      required: true,
      type: String,
    },
    canAdd: {
      required: true,
      type: Boolean,
    },
  },
  data() {
    return {
      paginatedActivities: {
        items: [],
        total: null,
        next_cursor: null,
      },
      pageCount: 0,
      modalOpen: false,
    };
  },
  computed: {
    hasMoreActivities() {
      return (
        this.paginatedActivities.next_cursor !== null &&
        this.paginatedActivities.next_cursor.length > 0
      );
    },
  },
  methods: {
    /**
     * Handler for a create mutation error.
     * Shows a generic saving error toast.
     */
    creationError() {
      notify({
        message: this.$str('toast_error_create_activity', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Open the create activity modal.
     */
    showCreateModal() {
      this.modalOpen = true;
    },

    /**
     * Close the create activity modal.
     */
    modalRequestClose() {
      this.modalOpen = false;
    },

    /**
     * Activity created observer.
     * @param {Object} activity
     */
    activityCreated(activity) {
      this.redirectToManageActivity(activity.id);
    },

    /**
     * Activity cloned observer.
     * @param {Object} clone
     * @param {Number} id
     */
    activityCloned({ clone, id }) {
      this.redirectToManageActivity(clone.id, {
        activity_cloned: true,
        cloned_activity_id: id,
      });
    },

    /**
     * Full page redirect to the management screen for a specific perform activity.
     * @param {Number} activityId
     * @param {Object} postParameters
     */
    redirectToManageActivity(activityId, postParameters) {
      const activityUrl = this.getEditActivityUrl(activityId);
      postParameters
        ? redirectWithPost(activityUrl, postParameters)
        : (window.location = activityUrl);
    },

    /**
     * Get the url to manage a specific perform activity.
     * @param {Number} activityId
     */
    getEditActivityUrl(activityId) {
      return this.$url(this.editUrl, { activity_id: activityId });
    },

    /**
     * Reload the activities list.
     */
    refetchActivities() {
      let queryVariables = this.getPaginationOption({
        limit: this.pageCount * DEFAULT_ITEMS_PER_PAGE,
      });
      let pageCount = this.pageCount;
      this.$apollo.queries.paginatedActivities
        .refetch(queryVariables)
        .then(() => {
          this.pageCount = pageCount;
        });
    },

    /**
     * Gets pagination options.
     * @param {Number} limit
     * @param {String|NULL} cursor
     */
    getPaginationOption({ limit, cursor }) {
      if (!cursor) {
        cursor = null;
      }

      let paginationOptions = {
        cursor,
      };

      if (limit) {
        paginationOptions.limit = limit;
      }

      return {
        query_options_input: {
          pagination: paginationOptions,
        },
      };
    },

    /**
     * Load more items to activities list.
     */
    loadMore() {
      this.$apollo.queries.paginatedActivities.fetchMore({
        variables: this.getPaginationOption({
          cursor: this.paginatedActivities.next_cursor,
        }),
        updateQuery: (previousResult, { fetchMoreResult }) => {
          fetchMoreResult.mod_perform_paginated_activities.items.unshift(
            ...this.paginatedActivities.items
          );

          return fetchMoreResult;
        },
      });
    },
  },

  apollo: {
    paginatedActivities: {
      query: performActivitiesQuery,
      variables() {
        return this.getPaginationOption({
          cursor: null,
        });
      },
      update(data) {
        this.pageCount++;

        return data.mod_perform_paginated_activities;
      },
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "add_activity",
      "perform:manage_activity",
      "showing_activities",
      "toast_error_create_activity",
      "view_actions",
      "view_name",
      "view_status",
      "view_type"
    ],
    "totara_core": [
      "loadmore"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performManageActivityList {
  & > * + * {
    margin-top: var(--gap-4);
  }

  &__wrapper {
    display: flex;
  }

  &__total {
    display: flex;
    align-items: center;
  }

  &__add {
    margin-left: auto;
  }

  &__loadMore {
    margin-top: var(--gap-2);
    text-align: center;
  }
}

@media (min-width: $tui-screen-sm) {
  .tui-performManageActivityList {
    & > * + * {
      margin-top: var(--gap-2);
    }
  }
}
</style>
