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
    <h2 v-text="$str('perform:manage_activity', 'mod_perform')" />
    <Button
      v-if="canAdd"
      :text="$str('add_activity', 'mod_perform')"
      @click="showCreateModal()"
    />
    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <Modal :aria-labelledby="$id('title')">
        <ModalContent
          :title="$str('add_activity', 'mod_perform')"
          :title-id="$id('title')"
          :close-button="true"
        >
          <GeneralInfoForm
            :submit-button-text="$str('get_started', 'mod_perform')"
            :disable-after-save="true"
            :use-modal-styling="true"
            @mutation-success="redirectToManageActivity"
            @mutation-error="creationError"
          />
        </ModalContent>
      </Modal>
    </ModalPresenter>

    <loader :loading="$apollo.loading">
      <Table
        v-if="!$apollo.loading"
        :data="activities"
        class="tui-performManageActivityList__table"
      >
        <template v-slot:header-row>
          <HeaderCell size="9">{{
            $str('view_name', 'mod_perform')
          }}</HeaderCell>
          <HeaderCell size="2">{{
            $str('view_type', 'mod_perform')
          }}</HeaderCell>
          <HeaderCell size="2">{{
            $str('view_status', 'mod_perform')
          }}</HeaderCell>
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
            <ActivityActions :activity="row" @refetch="refetchActivities" />
          </Cell>
        </template>
      </Table>
    </loader>
  </div>
</template>

<script>
import ActivityActions from 'mod_perform/components/activities_list/ActivityActions';
import Button from 'tui/components/buttons/Button';
import Cell from 'tui/components/datatable/Cell';
import GeneralInfoForm from 'mod_perform/components/manage_activity/GeneralInfoTab';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Loader from 'tui/components/loader/Loader';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import performActivitiesQuery from 'mod_perform/graphql/activities';
import Table from 'tui/components/datatable/Table';
import { notify } from 'tui/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    ActivityActions,
    Button,
    Cell,
    GeneralInfoForm,
    HeaderCell,
    Loader,
    Modal,
    ModalContent,
    ModalPresenter,
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
      activities: [],
      modalOpen: false,
    };
  },
  methods: {
    /**
     * Handler for a create mutation error.
     * Shows a generic saving error toast.
     */
    creationError() {
      notify({
        duration: NOTIFICATION_DURATION,
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
     * Full page redirect to the management screen for a specific perform activity.
     */
    redirectToManageActivity({ id }) {
      window.location = this.getEditActivityUrl(id);
    },

    /**
     * Get the url to manage a specific perform activity.
     */
    getEditActivityUrl(activityId) {
      return this.$url(this.editUrl, { activity_id: activityId });
    },

    /**
     * Reload the activities list.
     */
    refetchActivities() {
      this.$apollo.queries.activities.refetch();
    },
  },
  apollo: {
    activities: {
      query: performActivitiesQuery,
      update: data => data.mod_perform_activities,
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "add_activity",
      "get_started",
      "perform:manage_activity",
      "toast_error_create_activity",
      "view_actions",
      "view_name",
      "view_status",
      "view_type"
    ]
  }
</lang-strings>
