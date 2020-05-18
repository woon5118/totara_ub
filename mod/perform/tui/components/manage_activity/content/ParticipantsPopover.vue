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
  @package mod_perform
-->

<template>
  <Popover :triggers="['click']">
    <h2 class="sr-only">
      {{ $str('activity_participants_select_heading', 'mod_perform') }}
    </h2>
    <label :id="$id('participants-select-heading')">
      {{ $str('activity_participants_select_heading', 'mod_perform') }}
    </label>
    <Checkbox
      v-for="participant in availableParticipants"
      :key="participant.id"
      :value="participant.id"
      :name="participant.name"
      :checked="isActiveParticipant(participant)"
      :disabled="isActiveParticipant(participant)"
      @change="isChecked => handleChange(isChecked, participant)"
    >
      <template>
        {{ participant.name }}
      </template>
    </Checkbox>
    <template v-slot:buttons="{ close }">
      <Button
        :styleclass="{ small: true, primary: true }"
        :text="$str('activity_participants_select_done', 'mod_perform')"
        @click="updateParticipants(close)"
      />
      <Button
        :styleclass="{ small: true, primary: false }"
        :text="$str('cancel')"
        @click="close"
      />
    </template>
    <template v-slot:trigger>
      <ButtonIcon
        :styleclass="{ small: true }"
        :aria-label="$str('activity_participants_add', 'mod_perform')"
        :disabled="hasAddedAllParticipants"
      >
        <AddIcon size="100" />
      </ButtonIcon>
    </template>
  </Popover>
</template>
<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Checkbox from 'totara_core/components/form/Checkbox';
import Popover from 'totara_core/components/popover/Popover';
import RelationshipsQuery from 'totara_core/graphql/relationships.graphql';

export default {
  components: {
    AddIcon,
    Button,
    ButtonIcon,
    Checkbox,
    Popover,
  },

  props: {
    activeParticipants: {
      required: true,
      type: Array,
    },
  },

  data() {
    return {
      availableParticipants: [],
      checkedParticipants: [],
    };
  },

  computed: {
    hasAddedAllParticipants() {
      return (
        this.availableParticipants.length === this.activeParticipants.length
      );
    },
  },

  methods: {
    /**
     * Close the popover and notify the parent that a selection has been made or updated.
     * @param {function} close
     */
    updateParticipants(close) {
      close();
      this.$emit('update-participants', this.checkedParticipants);
      this.checkedParticipants = [];
    },

    isActiveParticipant(participant) {
      return this.activeParticipants
        .map(participant => participant.relationship.id)
        .includes(participant.id);
    },

    handleChange(isChecked, participant) {
      if (isChecked) {
        this.checkedParticipants.push(participant);
      } else {
        this.checkedParticipants = this.checkedParticipants.filter(
          value => value !== participant
        );
      }
    },
  },

  apollo: {
    availableParticipants: {
      query: RelationshipsQuery,
      update: data => data.totara_core_relationships,
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_participants_select_done",
      "activity_participants_select_heading",
      "activity_participants_add"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
