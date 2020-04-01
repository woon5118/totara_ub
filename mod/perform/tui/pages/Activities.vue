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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package totara_perform
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
      <Table v-if="!$apollo.loading" :data="activities">
        <template v-slot:header-row>
          <HeaderCell size="9">{{
            $str('view_name', 'mod_perform')
          }}</HeaderCell>
          <HeaderCell size="2">{{
            $str('view_status', 'mod_perform')
          }}</HeaderCell>
          <HeaderCell size="1" />
        </template>
        <template v-slot:row="{ row }">
          <Cell size="9" :column-header="$str('view_name', 'mod_perform')">
            <a :href="getEditActivityUrl(row.id)">{{ row.name }}</a>
          </Cell>
          <Cell size="2" :column-header="$str('view_status', 'mod_perform')">
            {{ $str('view_status_active', 'mod_perform') }}
          </Cell>
          <Cell size="1" :column-header="$str('view_actions', 'mod_perform')">
            <a
              v-if="row.can_view_participation_reporting"
              :href="getParticipationReportingUrl(row.id)"
              :title="$str('participation_reporting', 'mod_perform')"
            >
              <ParticipationReportingIcon
                :alt="$str('participation_reporting', 'mod_perform')"
                :title="$str('participation_reporting', 'mod_perform')"
                size="200"
              />
            </a>
          </Cell>
        </template>
      </Table>
    </loader>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Cell from 'totara_core/components/datatable/Cell';
import ParticipationReportingIcon from 'mod_perform/components/icons/ParticipationReporting';
import GeneralInfoForm from 'mod_perform/components/manage_activity/GeneralInfoForm';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Loader from 'totara_core/components/loader/Loader';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Table from 'totara_core/components/datatable/Table';
import performActivitiesQuery from 'mod_perform/graphql/activities.graphql';
import { notify } from 'totara_core/notifications';

const TOAST_DURATION = 10 * 1000; // in microseconds.

export default {
  components: {
    Button,
    Cell,
    HeaderCell,
    Table,
    ModalPresenter,
    GeneralInfoForm,
    Loader,
    Modal,
    ModalContent,
    ParticipationReportingIcon,
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
        duration: TOAST_DURATION,
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
      return `${this.editUrl}?activity_id=${activityId}`;
    },

    /**
     * Get the url to the participation tracking
     *
     * @param activityId
     * @return {string}
     */
    getParticipationReportingUrl(activityId) {
      const params = { activity_id: activityId };
      return this.$url(
        '/mod/perform/reporting/participation/index.php',
        params
      );
    },
  },
  apollo: {
    activities: {
      query: performActivitiesQuery,
      variables() {
        return [];
      },
      update: data => data.mod_perform_activities,
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "get_started",
      "participation_reporting",
      "add_activity",
      "perform:manage_activity",
      "view_actions",
      "view_name",
      "view_status",
      "view_status_active",
      "toast_error_create_activity"
    ]
  }
</lang-strings>
