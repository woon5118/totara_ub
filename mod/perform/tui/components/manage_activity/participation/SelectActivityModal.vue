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
  @package mod_perform
-->

<template>
  <ModalPresenter :open="isOpen" @request-close="close">
    <Modal>
      <ModalContent
        :title="$str('manage_participation_select_activity', 'mod_perform')"
      >
        <Loader :loading="$apollo.loading">
          <Select
            v-if="mightHaveOptions"
            v-model="selectedActivityId"
            :options="options"
            name="manage-participation-activity-select"
          />
          <p v-else>
            {{ $str('manage_participation_no_activities', 'mod_perform') }}
          </p>
        </Loader>

        <template v-slot:buttons>
          <ActionLink
            v-if="mightHaveOptions"
            :href="continueHref"
            :text="$str('button_continue', 'mod_perform')"
            :disabled="!selectedActivityId"
            :styleclass="{ primary: true }"
          />
          <Button :text="$str('button_close', 'mod_perform')" @click="close" />
        </template>
      </ModalContent>
    </Modal>
  </ModalPresenter>
</template>

<script>
import ActionLink from 'totara_core/components/links/ActionLink';
import Button from 'totara_core/components/buttons/Button';
import Loader from 'totara_core/components/loader/Loader';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Select from 'totara_core/components/form/Select';
import participantManageableActivitiesQuery from 'mod_perform/graphql/participant_manageable_activities';

export default {
  components: {
    ActionLink,
    Button,
    Loader,
    Modal,
    ModalContent,
    ModalPresenter,
    Select,
  },
  props: {
    value: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      isOpen: false,
      selectedActivityId: null,
      activities: [],
    };
  },
  computed: {
    mightHaveOptions() {
      if (this.$apollo.loading) {
        return true;
      }

      return this.activities.length > 0;
    },
    options() {
      return this.activities.map(activity => {
        return {
          id: activity.id,
          label: activity.name,
        };
      });
    },
    continueHref() {
      if (!this.selectedActivityId) {
        return '#';
      }

      return this.$url(
        `/mod/perform/manage/participation/subject_instances.php?activity_id=${this.selectedActivityId}`
      );
    },
  },
  mounted() {
    if (this.value) {
      this.open();
    }
  },
  apollo: {
    activities: {
      query: participantManageableActivitiesQuery,
      update(data) {
        const activities =
          data['mod_perform_participant_manageable_activities'];

        if (activities.length > 0) {
          this.selectedActivityId = activities[0].id;
        }

        return activities;
      },
    },
  },
  methods: {
    open() {
      this.isOpen = true;
    },
    close() {
      this.isOpen = false;
      this.$emit('input', false);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "button_close",
      "button_continue",
      "manage_participation",
      "manage_participation_select_activity",
      "manage_participation_no_activities"
    ]
  }
</lang-strings>
