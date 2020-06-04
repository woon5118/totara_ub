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
  <Card
    class="tui-performActivitySection"
    :no-border="autoSave"
    :class="[!autoSave && 'tui-performActivitySection__multiple']"
  >
    <div
      :class="[editMode && !autoSave && 'tui-performActivitySection__editing']"
    >
      <Grid v-if="!autoSave && !editMode">
        <GridItem :units="10">
          <h3>
            {{ section.title || $str('untitled_section', 'mod_perform') }}
          </h3>
        </GridItem>
        <GridItem :units="2">
          <div class="tui-performActivitySection__action-buttons">
            <EditIcon
              class="tui-performActivitySection__action-edit"
              @click="enableEditing"
            />
            <Dropdown position="bottom-right">
              <template v-slot:trigger="{ toggle }">
                <ButtonIcon
                  :styleclass="{
                    transparentNoPadding: true,
                  }"
                  :aria-label="$str('section_dropdown_menu', 'mod_perform')"
                  @click="toggle"
                >
                  <ActivityActionsIcon size="200" />
                </ButtonIcon>
              </template>
              <DropdownItem :disabled="isAdding" @click="$emit('add_above')">
                {{ $str('section_action_add_above', 'mod_perform') }}
              </DropdownItem>
              <DropdownItem
                v-if="!lastSection"
                :disabled="isAdding"
                @click="$emit('add_below')"
              >
                {{ $str('section_action_add_below', 'mod_perform') }}
              </DropdownItem>
            </Dropdown>
          </div>
        </GridItem>
      </Grid>
      <InputText
        v-if="!autoSave && editMode"
        :value="title"
        :placeholder="$str('untitled_section', 'mod_perform')"
        :aria-label="$str('section_title', 'mod_perform')"
        @input="updateTitle"
      />
      <div class="tui-performActivitySection__participant">
        <h4 class="tui-performActivitySection__participant-heading">
          {{ $str('activity_participants_heading', 'mod_perform') }}
        </h4>
        <span v-if="!autoSave && !editMode">
          {{ $str('activity_participant_view_other_responses', 'mod_perform') }}
        </span>
        <div class="tui-performActivitySection__participant-items">
          <ActivitySectionRelationship
            v-for="participant in displayedParticipantsSorted"
            :key="participant.relationship.id"
            :participant="participant"
            :editable="autoSave || editMode"
            @participant-removed="removeDisplayedParticipant"
            @can-view-changed="updateParticipantData"
          />
        </div>

        <div
          v-if="
            displayedParticipantsSorted.length === 0 && !autoSave && !editMode
          "
        >
          <span class="tui-performActivitySection__participant-info">
            {{ $str('no_participants_added', 'mod_perform') }}
          </span>
        </div>
        <ParticipantsPopover
          v-if="autoSave || editMode"
          :active-participants="displayedParticipants"
          @update-participants="updateDisplayedParticipants"
        />
      </div>
    </div>
    <ButtonGroup
      v-if="!autoSave && editMode"
      class="tui-performActivitySection__saveButtons"
    >
      <Button
        :styleclass="{ primary: true }"
        :text="$str('activity_section_done', 'mod_perform')"
        :disabled="isSaving || !editMode"
        @click="trySave"
      />
      <Button
        :text="$str('cancel')"
        :disabled="isSaving || !editMode"
        @click="resetSectionChanges"
      />
    </ButtonGroup>
    <div
      class="tui-performActivitySection__content"
      :class="[autoSave && 'tui-performActivitySection__content-autoSave']"
    >
      <Grid :stack-at="700">
        <GridItem grows :units="10">
          <ActivitySectionElementSummary
            v-if="section.section_elements_summary"
            :elements-summary="section.section_elements_summary"
          />
        </GridItem>
        <GridItem grows :units="2">
          <div class="tui-performActivitySection__content-buttons">
            <Button
              :text="$str('edit_content_elements', 'mod_perform')"
              :aria-label="$str('edit_content_elements', 'mod_perform')"
              @click="modalOpen = true"
            />
          </div>
        </GridItem>
      </Grid>
    </div>

    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <EditSectionContentModal
        :section-id="section.id"
        @mutation-success="showMutationSuccessNotification"
        @mutation-error="showMutationErrorNotification"
        @update-summary="updateSection"
      />
    </ModalPresenter>
  </Card>
</template>

<script>
import ActivityActionsIcon from 'mod_perform/components/icons/ActivityActions';
import ActivitySectionElementSummary from 'mod_perform/components/manage_activity/content/ActivitySectionElementSummary';
import ActivitySectionRelationship from 'mod_perform/components/manage_activity/content/ActivitySectionRelationship';
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Card from 'totara_core/components/card/Card';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import EditIcon from 'totara_core/components/buttons/EditIcon';
import EditSectionContentModal from 'mod_perform/components/manage_activity/content/EditSectionContentModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import InputText from 'totara_core/components/form/InputText';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import ParticipantsPopover from 'mod_perform/components/manage_activity/content/ParticipantsPopover';
import UpdateSectionRelationshipsMutation from 'mod_perform/graphql/update_section_relationships.graphql';

export default {
  components: {
    ActivityActionsIcon,
    ActivitySectionElementSummary,
    ActivitySectionRelationship,
    Button,
    ButtonGroup,
    ButtonIcon,
    Card,
    Dropdown,
    DropdownItem,
    EditIcon,
    EditSectionContentModal,
    Grid,
    GridItem,
    InputText,
    ModalPresenter,
    ParticipantsPopover,
  },

  props: {
    autoSave: {
      type: Boolean,
      default: false,
    },
    editMode: {
      type: Boolean,
      default: false,
    },
    section: {
      type: Object,
      required: true,
    },
    lastSection: {
      type: Boolean,
      required: true,
    },
    isAdding: {
      type: Boolean,
      required: true,
    },
    sortOrder: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      modalOpen: false,
      savedSection: this.section,
      displayedParticipants: this.getParticipantsFromSection(this.section),
      title: this.getTitle(),
      isSaving: false,
    };
  },

  computed: {
    /**
     * Get saved participants.
     *
     * @return {Array}
     */
    savedParticipants() {
      return this.getParticipantsFromSection(this.savedSection);
    },

    /**
     * Has anything changed compared to last saved state?
     * Checks for difference between displayed & last saved participants arrays.
     *
     * @return {Boolean}
     */
    hasChanges: function() {
      return !(
        this.title === this.section.title &&
        this.displayedParticipants.length === this.savedParticipants.length &&
        this.displayedParticipants.every(value => {
          return this.savedParticipants.find(participant => {
            return (
              participant.relationship.id === value.relationship.id &&
              participant.can_view === value.can_view
            );
          });
        })
      );
    },

    /**
     * Gets Sorted list of the displayed participants.
     *
     * @return {Array}
     */
    displayedParticipantsSorted() {
      return this.displayedParticipants
        .slice()
        .sort((a, b) => a.relationship.id - b.relationship.id);
    },
  },

  methods: {
    /**
     * Gets section relationships.
     *
     * @return {Array}
     */
    getParticipantsFromSection(section) {
      if (section.section_relationships) {
        return section.section_relationships;
      }
      return [];
    },

    /**
     * Get section title.
     *
     * @return {string}
     */
    getTitle() {
      return this.section.title;
    },

    /**
     * Get section relationships.
     *
     * @return {Array}
     */
    getSectionRelationships() {
      return this.displayedParticipants.map(participant => {
        return {
          id: participant.relationship.id,
          can_view: participant.can_view,
        };
      });
    },

    /**
     * Update section.
     */
    updateSection(update) {
      const newValue = Object.assign({}, this.section, update);
      this.$emit('input', newValue);
    },
    /**
     * Update section title.
     */
    updateTitle(update) {
      this.title = update;
    },

    /**
     * Enable edit-mode on section.
     */
    enableEditing() {
      this.$emit('toggle-edit-mode', true);
    },

    /**
     * Disable edit-mode on section.
     */
    disableEditing() {
      this.$emit('toggle-edit-mode', false);
    },

    /**
     * Close edit section content modal.
     */
    modalRequestClose() {
      this.modalOpen = false;
    },

    /**
     * Update the displayed participants.
     * @param {Array} checkedParticipants List of new participants
     */
    updateDisplayedParticipants(checkedParticipants) {
      this.displayedParticipants = this.displayedParticipants.concat(
        checkedParticipants.map(participant => {
          return {
            can_view: false,
            relationship: participant,
          };
        })
      );
      if (this.autoSave) {
        this.trySave();
      }
    },

    /**
     * Update data of a participant.
     * @param {Object} participant
     */
    updateParticipantData(participant) {
      this.displayedParticipants = this.displayedParticipants.map(value => {
        return value.relationship.id === participant.relationship.id
          ? participant
          : value;
      });
      if (this.autoSave) {
        this.trySave();
      }
    },

    /**
     * Remove participant from displayed participant.
     * @param {Object} participant
     */
    removeDisplayedParticipant(participant) {
      this.displayedParticipants = this.displayedParticipants.filter(
        value => value.relationship.id !== participant.relationship.id
      );
      if (this.autoSave) {
        this.trySave();
      }
    },

    /**
     * Reset changes made in the section.
     */
    resetSectionChanges() {
      this.displayedParticipants = this.getParticipantsFromSection(
        this.savedSection
      );
      this.disableEditing();
    },

    /**
     * Show success notification.
     */
    showMutationSuccessNotification() {
      this.$emit('mutation-success');
    },

    /**
     * Show error notification.
     */
    showMutationErrorNotification() {
      this.$emit('mutation-error');
    },

    /**
     * Save section changes.
     */
    async trySave() {
      this.isSaving = true;

      try {
        if (this.hasChanges) {
          const savedSection = await this.save();
          this.updateSection(savedSection);
          this.$emit('mutation-success');
        }
        this.isSaving = false;
        this.disableEditing();
      } catch (e) {
        this.$emit('mutation-error', e);
        this.isSaving = false;
        this.disableEditing();
      }
    },

    /**
     * Mutation call to save changes.
     * @return {Object}
     */
    async save() {
      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateSectionRelationshipsMutation,
        variables: {
          input: {
            section_id: this.section.id,
            relationships: this.getSectionRelationships(),
          },
        },
        refetchAll: false, // Don't refetch all the data again
      });

      const result = resultData['mod_perform_update_section_relationships'];
      this.savedSection = result.section;
      return result;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_participant_view_other_responses",
      "activity_participants_heading",
      "activity_section_done",
      "edit_content_elements",
      "no_participants_added",
      "section_action_add_above",
      "section_action_add_below",
      "section_dropdown_menu",
      "section_title",
      "untitled_section"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
