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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="mod-perform-activitySection__participant-grid">
    <div
      class="mod-perform-activitySection__participant-grid-item mod-perform-activitySection__participant-name"
    >
      {{ participant.relationship.name }}
      <ButtonIcon
        :styleclass="{ small: true, transparent: true }"
        :aria-label="$str('delete')"
        @click="removeDisplayedParticipant(participant)"
      >
        <DeleteIcon />
      </ButtonIcon>
    </div>
    <div
      class="mod-perform-activitySection__participant-grid-item mod-perform-activitySection__participant-options"
    >
      <Checkbox
        v-model="participant_can_view"
        :label="
          $str('activity_participant_view_other_responses', 'mod_perform')
        "
        @change="toggleCanViewOnParticipant"
      >
        {{ $str('activity_participant_view_other_responses', 'mod_perform') }}
      </Checkbox>
    </div>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Checkbox from 'totara_core/components/form/Checkbox';
import DeleteIcon from 'totara_core/components/icons/common/Delete';

export default {
  components: {
    ButtonIcon,
    Checkbox,
    DeleteIcon,
  },
  props: {
    participant: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      participant_can_view: this.getCanView(),
    };
  },
  methods: {
    getCanView() {
      return this.participant.can_view;
    },
    toggleCanViewOnParticipant(checked) {
      const participant = Object.assign({}, this.participant, {
        can_view: checked,
      });
      this.$emit('can-view-changed', participant);
    },
    removeDisplayedParticipant(participant) {
      this.$emit('participant-removed', participant);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_participant_view_other_responses"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
