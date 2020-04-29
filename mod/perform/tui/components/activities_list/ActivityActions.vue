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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_perform
-->

<template>
  <div class="tui-performActivityActions__actionIcons">
    <a
      v-if="activity.can_view_participation_reporting"
      :href="participationReportingUrl"
      :title="$str('participation_reporting', 'mod_perform')"
    >
      <ParticipationReportingIcon
        :alt="$str('participation_reporting', 'mod_perform')"
        :title="$str('participation_reporting', 'mod_perform')"
        size="200"
      />
    </a>

    <Dropdown position="bottom-right">
      <template v-slot:trigger="{ toggle }">
        <a href="#" @click.prevent="toggle">
          <ActivityActionsIcon
            :alt="$str('activity_action_options', 'mod_perform')"
            :title="$str('activity_action_options', 'mod_perform')"
            size="200"
          />
        </a>
      </template>
      <DropdownItem
        v-if="activity.can_potentially_activate"
        :disabled="!activity.can_activate"
        :title="activateOptionTitle"
        @click="showActivateModal"
      >
        {{ $str('activity_action_activate', 'mod_perform') }}
      </DropdownItem>
      <DropdownItem>{{
        $str('activity_action_delete', 'mod_perform')
      }}</DropdownItem>
    </Dropdown>

    <ConfirmationModal
      :open="activateModalOpen"
      :title="$str('modal_activate_title', 'mod_perform')"
      @confirm="activateActivity"
      @cancel="closeActivateModal"
    >
      <span v-html="$str('modal_activate_message', 'mod_perform')" />
    </ConfirmationModal>
  </div>
</template>

<script>
import ActivityActionsIcon from 'mod_perform/components/icons/ActivityActions';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import ParticipationReportingIcon from 'mod_perform/components/icons/ParticipationReporting';

export default {
  components: {
    ActivityActionsIcon,
    ConfirmationModal,
    Dropdown,
    DropdownItem,
    ParticipationReportingIcon,
  },

  props: {
    activity: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      activateModalOpen: false,
    };
  },

  computed: {
    /**
     * Get the url to the participation tracking
     *
     * @return {string}
     */
    participationReportingUrl() {
      return this.$url('/mod/perform/reporting/participation/index.php', {
        activity_id: this.activity.id,
      });
    },

    /**
     * For certain cases, get a title text for the 'Activate" dropdown option.
     */
    activateOptionTitle() {
      if (
        this.activity.can_potentially_activate &&
        !this.activity.can_activate
      ) {
        return this.$str('activity_draft_not_ready', 'mod_perform');
      }
      return this.$str('activity_action_activate', 'mod_perform');
    },
  },

  methods: {
    /**
     * Display the modal for confirming the activation of the activity
     */
    showActivateModal() {
      this.activateModalOpen = true;
    },

    /**
     * Close the modal for confirming the activation of the activity
     */
    closeActivateModal() {
      this.activateModalOpen = false;
    },

    /**
     * Activate an activity
     */
    activateActivity() {
      console.log(this.activity.id);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_action_activate",
      "activity_action_delete",
      "activity_action_options",
      "activity_draft_not_ready",
      "modal_activate_message",
      "modal_activate_title",
      "participation_reporting"
    ]
  }
</lang-strings>
