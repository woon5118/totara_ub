<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performActivitySectionRelationship">
    <div v-if="editable" class="tui-performActivitySectionRelationship__item">
      <div class="tui-performActivitySectionRelationship__item-name">
        {{ participant.core_relationship.name }}
        <DeleteIcon
          :aria-label="
            $str(
              'delete_relationship',
              'mod_perform',
              participant.core_relationship.name
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
        {{ participant.core_relationship.name
        }}{{ participant.can_view ? '*' : '' }}
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
