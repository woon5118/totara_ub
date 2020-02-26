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
    <!-- TODO use alert component when created -->
    <div
      v-if="mutationError"
      class="alert alert-danger alert-with-icon alert-dismissable fade-in"
      role="alert"
    >
      <button type="button" class="close" data-dismiss="alert">
        <FlexIcon icon="delete-ns" />
      </button>
      <div class="alert-icon">
        <FlexIcon icon="notification-error" />
      </div>
      <div class="alert-message">
        {{ $str('error_generic_mutation', 'mod_perform') }}
      </div>
    </div>

    <h2 v-text="$str('perform:manage_activity', 'mod_perform')" />
    <Button
      v-if="canAdd"
      :text="$str('perform:add_activity', 'mod_perform')"
      @click="showCreateModal()"
    />
    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <Modal :aria-labelledby="$id('title')">
        <ModalContent
          :title="$str('perform:add_activity', 'mod_perform')"
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
          <HeaderCell size="8">{{
            $str('perform:view:name', 'mod_perform')
          }}</HeaderCell>
          <HeaderCell size="4">{{
            $str('perform:view:status', 'mod_perform')
          }}</HeaderCell>
        </template>
        <template v-slot:row="{ row }">
          <Cell
            size="8"
            :column-header="$str('perform:view:name', 'mod_perform')"
          >
            <a :href="getEditActivityUrl(row.id)">{{ row.name }}</a>
          </Cell>
          <Cell
            size="4"
            :column-header="$str('perform:view:status', 'mod_perform')"
          >
            {{ $str('perform:view:status:active', 'mod_perform') }}
          </Cell>
        </template>
      </Table>
    </loader>
  </div>
</template>

<script>
import Table from 'totara_core/components/datatable/Table';
import Cell from 'totara_core/components/datatable/Cell';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import performActivitiesQuery from 'mod_perform/graphql/activities.graphql';
import Button from 'totara_core/components/buttons/Button';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import GeneralInfoForm from 'mod_perform/components/manage_activity/GeneralInfoForm';
import Loader from 'totara_core/components/loader/Loader';

export default {
  components: {
    Button,
    Cell,
    FlexIcon,
    HeaderCell,
    Table,
    ModalPresenter,
    GeneralInfoForm,
    Loader,
    Modal,
    ModalContent,
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
      mutationError: null,
    };
  },
  methods: {
    /**
     * Handler for a create mutation error.
     *
     * @param {Error} e
     */
    creationError(e) {
      this.mutationError = e;
      this.modalOpen = false;
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
      "perform:add_activity",
      "get_started",
      "perform:manage_activity",
      "perform:view:name",
      "perform:view:status:active",
      "perform:view:status"
    ]
  }
</lang-strings>
