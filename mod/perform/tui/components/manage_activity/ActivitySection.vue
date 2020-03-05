<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theN
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package totara_perform
-->

<template>
  <Card class="mod-perform-activitySection">
    <Grid>
      <GridItem :units="8" />
      <GridItem :units="4">
        <div class="mod-perform-activitySection__action-buttons">
          <Button
            :text="$str('edit_content', 'mod_perform')"
            :styleclass="{ small: true }"
            @click="modelOpen = true"
          />
        </div>
      </GridItem>
    </Grid>
    <Grid :stack-at="768">
      <GridItem :units="5">
        <h3 class="mod-perform-activitySection__participant-heading">
          {{ $str('activity_participants:heading', 'mod_perform') }}
        </h3>
        <div
          v-for="(participant, key) in displayedParticipants"
          :key="key"
          class="mod-perform-activitySection__participant-row"
        >
          {{ participant }}
          <ButtonIcon
            :styleclass="{ small: true, transparent: true }"
            :aria-label="$str('delete')"
            @click="removeDisplayedParticipant(participant)"
          >
            <DeleteIcon />
          </ButtonIcon>
        </div>

        <ParticipantsPopover
          :active-participants="displayedParticipants"
          @update-participants="updateDisplayedParticipants"
        >
          <template v-slot:rating-trigger>
            <ButtonIcon
              :styleclass="{ small: true }"
              :aria-label="$str('activity_participants:add', 'mod_perform')"
            >
              <AddIcon size="100" />
            </ButtonIcon>
          </template>
        </ParticipantsPopover>

        <br />
        <Button
          :styleclass="{ primary: true }"
          :text="$str('activity_section:save_changes', 'mod_perform')"
          :disabled="isSaving || !hasChanges"
          @click="trySave"
        />
        <Button
          :styleclass="{ primary: false }"
          :text="$str('cancel')"
          :disabled="isSaving || !hasChanges"
          @click="resetSectionChanges"
        />
      </GridItem>
    </Grid>

    <ModalPresenter :open="modelOpen" @request-close="modalRequestClose">
      <EditSectionContentModal :section-id="section.id" />
    </ModalPresenter>
  </Card>
</template>

<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Card from 'totara_core/components/card/Card';
import DeleteIcon from 'totara_core/components/icons/common/Delete';
import EditSectionContentModal from 'mod_perform/components/manage_activity/EditSectionContentModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import ParticipantsPopover from 'mod_perform/components/manage_activity/ParticipantsPopover';
import UpdateSectionRelationshipsMutation from 'mod_perform/graphql/update_section_relationships.graphql';

export default {
  components: {
    AddIcon,
    Button,
    ButtonIcon,
    Card,
    DeleteIcon,
    EditSectionContentModal,
    Grid,
    GridItem,
    ModalPresenter,
    ParticipantsPopover,
  },
  props: {
    section: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      modelOpen: false,
      displayedParticipants: this.getLastSavedParticipants(),
      isSaving: false,
    };
  },
  computed: {
    // Has anything changed compared to last saved state?
    // Checks for difference between displayed & last saved participants arrays.
    hasChanges: function() {
      const lastSavedParticipants = this.getLastSavedParticipants();
      return !(
        this.displayedParticipants.length === lastSavedParticipants.length &&
        this.displayedParticipants.every(value =>
          lastSavedParticipants.includes(value)
        )
      );
    },
  },
  methods: {
    getLastSavedParticipants() {
      if (this.section.section_relationships) {
        return this.section.section_relationships.map(value => value.name);
      }
      return [];
    },
    updateSection(update) {
      const newValue = Object.assign({}, this.section, update);
      this.$emit('input', newValue);
    },
    modalRequestClose() {
      this.modelOpen = false;
    },
    updateDisplayedParticipants(checkedParticipants) {
      this.displayedParticipants = this.displayedParticipants.concat(
        checkedParticipants
      );
    },
    removeDisplayedParticipant(participant) {
      this.displayedParticipants = this.displayedParticipants.filter(
        value => value !== participant
      );
    },
    resetSectionChanges() {
      this.displayedParticipants = this.getLastSavedParticipants();
    },

    async trySave() {
      this.isSaving = true;

      try {
        const savedSection = await this.save();
        this.updateSection(savedSection);
        this.$emit('mutation-success');
      } catch (e) {
        this.$emit('mutation-error', e);
      } finally {
        this.isSaving = false;
      }
    },

    async save() {
      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateSectionRelationshipsMutation,
        variables: {
          input: {
            section_id: this.section.id,
            names: this.displayedParticipants,
          },
        },
        refetchAll: false, // Don't refetch all the data again
      });

      return resultData['mod_perform_update_section_relationships'];
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "edit_content",
      "activity_participants:add",
      "activity_participants:heading",
      "activity_section:save_changes"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
