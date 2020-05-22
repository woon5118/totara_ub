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
  <div class="tui-performActivitySectionRelationship">
    <div v-if="editable" class="tui-performActivitySectionRelationship__item">
      <div class="tui-performActivitySectionRelationship__item-name">
        {{ participant.relationship.name }}
        <DeleteIcon
          :aria-label="
            $str(
              'delete_relationship',
              'mod_perform',
              participant.relationship.name
            )
          "
          @click="removeDisplayedParticipant(participant)"
        />
      </div>
      <div class="tui-performActivitySectionRelationship__item-options">
        <Checkbox
          v-model="participant_can_view"
          @change="toggleCanViewOnParticipant"
        >
          {{ $str('activity_participant_can_view', 'mod_perform') }}
        </Checkbox>
      </div>
    </div>
    <div v-else class="tui-performActivitySectionRelationship__item">
      <p>
        {{ participant.relationship.name }}{{ participant.can_view ? '*' : '' }}
      </p>
    </div>
  </div>
</template>

<script>
import Checkbox from 'totara_core/components/form/Checkbox';
import DeleteIcon from 'totara_core/components/buttons/DeleteIcon';

export default {
  components: {
    Checkbox,
    DeleteIcon,
  },
  props: {
    participant: {
      type: Object,
      required: true,
    },
    editable: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      participant_can_view: this.getCanView(),
    };
  },
  methods: {
    /**
     * Gets can_view property of participant.
     *
     * @return {Boolean}
     */
    getCanView() {
      return this.participant.can_view;
    },
    /**
     * Change can_view property on participant.
     * @param {Boolean} checked Value to change can_view to
     */
    toggleCanViewOnParticipant(checked) {
      const participant = Object.assign({}, this.participant, {
        can_view: checked,
      });
      this.$emit('can-view-changed', participant);
    },
    /**
     * Removes displayed participant.
     * @param {Object} participant
     */
    removeDisplayedParticipant(participant) {
      this.$emit('participant-removed', participant);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_participant_can_view",
      "delete_relationship"
    ]
  }
</lang-strings>
