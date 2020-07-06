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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <ActionCard>
    <template v-slot:card-body>
      <!-- Draft Message -->
      <template v-if="state === ACTIVITY_STATUS_DRAFT">
        <span v-html="$str('activity_status_banner_draft', 'mod_perform')" />
        <HelpIcon
          position="bottom"
          :icon-label="$str('activity_status_banner_help_title', 'mod_perform')"
          :desc-id="$id('activation-help')"
        >
          <p>{{ $str('activity_status_banner_help_intro', 'mod_perform') }}</p>
          <ul>
            <li>
              {{ $str('activation_criteria_assignments', 'mod_perform') }}
            </li>
            <li>{{ $str('activation_criteria_elements', 'mod_perform') }}</li>
            <li>
              {{ $str('activation_criteria_relationships', 'mod_perform') }}
            </li>
            <li>{{ $str('activation_criteria_schedule', 'mod_perform') }}</li>
          </ul>
        </HelpIcon>
      </template>

      <!-- Active Message -->
      <span
        v-else-if="state === ACTIVITY_STATUS_ACTIVE"
        v-html="$str('activity_status_banner_active', 'mod_perform')"
      />
    </template>

    <template v-if="activity.can_potentially_activate" v-slot:card-action>
      <ActivateActivityModal :activity="activity" @refetch="$emit('refetch')">
        <template v-slot:trigger="{ open, loading }">
          <Button
            :text="$str('activity_action_activate', 'mod_perform')"
            :disabled="loading"
            @click="open"
          />
        </template>
      </ActivateActivityModal>
    </template>
  </ActionCard>
</template>

<script>
import ActionCard from 'totara_core/components/card/ActionCard';
import ActivateActivityModal from 'mod_perform/components/manage_activity/ActivateActivityModal';
import Button from 'totara_core/components/buttons/Button';
import HelpIcon from 'totara_core/components/form/HelpIcon';
import {
  ACTIVITY_STATUS_ACTIVE,
  ACTIVITY_STATUS_DRAFT,
} from 'mod_perform/constants';

export default {
  components: {
    ActionCard,
    ActivateActivityModal,
    Button,
    HelpIcon,
  },

  props: {
    activity: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      ACTIVITY_STATUS_ACTIVE,
      ACTIVITY_STATUS_DRAFT,
    };
  },

  computed: {
    /**
     * State name.
     * @returns {String}
     */
    state() {
      return this.activity.state_details.name;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activation_criteria_assignments",
      "activation_criteria_elements",
      "activation_criteria_relationships",
      "activation_criteria_schedule",
      "activity_action_activate",
      "activity_status_banner_active",
      "activity_status_banner_draft",
      "activity_status_banner_help_intro",
      "activity_status_banner_help_title"
    ]
  }
</lang-strings>
