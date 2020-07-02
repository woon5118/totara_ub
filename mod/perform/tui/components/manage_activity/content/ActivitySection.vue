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
          <h4 class="tui-performActivitySection__title">
            {{ savedSection.display_title }}
          </h4>
        </GridItem>
        <GridItem :units="2">
          <div class="tui-performActivitySection__action-buttons">
            <EditIcon
              class="tui-performActivitySection__action-edit"
              :aria-label="$str('edit_section', 'mod_perform')"
              @click="enableEditing"
            />
            <Dropdown position="bottom-right">
              <template v-slot:trigger="{ toggle }">
                <MoreButton
                  :aria-label="$str('section_dropdown_menu', 'mod_perform')"
                  :no-padding="true"
                  @click="toggle"
                />
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
              <DropdownItem v-if="canDelete" @click="showDeleteModal">
                {{ $str('section_action_delete', 'mod_perform') }}
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
        :maxlength="TITLE_INPUT_MAX_LENGTH"
        @input="title = $event"
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
            :key="participant.core_relationship.id"
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
          :relationships="relationships"
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
        :styleclass="{ primary: true, small: true }"
        :text="$str('activity_section_done', 'mod_perform')"
        :disabled="isSaving || !editMode || !hasChanges"
        @click="trySave"
      />
      <Button
        :text="$str('cancel')"
        :styleclass="{ small: true }"
        :disabled="isSaving || !editMode"
        @click="resetSectionChanges"
      />
    </ButtonGroup>
    <div
      class="tui-performActivitySection__content"
      :class="[autoSave && 'tui-performActivitySection__content-autoSave']"
    >
      <Grid :stack-at="700">
        <GridItem grows :units="9">
          <ActivitySectionElementSummary
            v-if="section.section_elements_summary"
            :elements-summary="section.section_elements_summary"
          />
        </GridItem>
        <GridItem grows :units="3">
          <div class="tui-performActivitySection__content-buttons">
            <EditSectionContentModal
              :section-id="section.id"
              :title="
                multipleSectionsEnabled
                  ? savedSection.display_title
                  : activityName
              "
              @update-summary="updateSection"
            >
              <template v-slot:trigger="{ open }">
                <Button
                  :styleclass="{ small: true }"
                  :text="$str('edit_content_elements', 'mod_perform')"
                  :aria-label="$str('edit_content_elements', 'mod_perform')"
                  @click="open"
                />
              </template>
            </EditSectionContentModal>
          </div>
        </GridItem>
      </Grid>
    </div>

    <ConfirmationModal
      :open="deleteSectionModalOpen"
      :title="$str('modal_section_delete_title', 'mod_perform')"
      :confirm-button-text="$str('delete')"
      :close-button="false"
      :loading="deleting"
      @confirm="deleteSection"
      @cancel="closeDeleteSectionModal"
    >
      <template>
        <p>{{ $str('modal_section_delete_message', 'mod_perform') }}</p>
      </template>
    </ConfirmationModal>
  </Card>
</template>

<script>
import ActivitySectionElementSummary from 'mod_perform/components/manage_activity/content/ActivitySectionElementSummary';
import ActivitySectionRelationship from 'mod_perform/components/manage_activity/content/ActivitySectionRelationship';
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Card from 'totara_core/components/card/Card';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import DeleteSectionMutation from 'mod_perform/graphql/delete_section.graphql';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import EditIcon from 'totara_core/components/buttons/EditIcon';
import EditSectionContentModal from 'mod_perform/components/manage_activity/content/EditSectionContentModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import InputText from 'totara_core/components/form/InputText';
import MoreButton from 'totara_core/components/buttons/MoreIcon';
import ParticipantsPopover from 'mod_perform/components/manage_activity/content/ParticipantsPopover';
import UpdateSectionSettingsMutation from 'mod_perform/graphql/update_section_settings.graphql';

/**
 * Reflects the maximum length of the field in the database.
 * @type {number}
 */
const TITLE_INPUT_MAX_LENGTH = 1024;

export default {
  components: {
    ActivitySectionElementSummary,
    ActivitySectionRelationship,
    Button,
    ButtonGroup,
    Card,
    ConfirmationModal,
    Dropdown,
    DropdownItem,
    EditIcon,
    EditSectionContentModal,
    Grid,
    GridItem,
    InputText,
    MoreButton,
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
    relationships: {
      type: Array,
      required: true,
    },
    sectionCount: {
      type: Number,
      required: true,
    },
    activityName: {
      type: String,
      required: true,
    },
    multipleSectionsEnabled: {
      type: Boolean,
      required: true,
    },
  },

  data() {
    return {
      savedSection: this.section,
      displayedParticipants: this.getParticipantsFromSection(this.section),
      title: this.section.title,
      isSaving: false,
      TITLE_INPUT_MAX_LENGTH,
      deleteSectionModalOpen: false,
      deleting: false,
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
     * Checks for difference between displayed & last saved participants arrays, and changes to the title.
     *
     * @return {Boolean}
     */
    hasChanges() {
      if (this.isNew) {
        return true;
      }

      if (this.title !== this.savedSection.title) {
        return true;
      }

      if (this.displayedParticipants.length !== this.savedParticipants.length) {
        return true;
      }

      return !this.displayedParticipants.every(value => {
        return this.savedParticipants.find(participant => {
          return (
            participant.core_relationship.id === value.core_relationship.id &&
            participant.can_view === value.can_view
          );
        });
      });
    },

    /**
     * Gets Sorted list of the displayed participants.
     *
     * @return {Array}
     */
    displayedParticipantsSorted() {
      return this.displayedParticipants
        .slice()
        .sort((a, b) => a.core_relationship.id - b.core_relationship.id);
    },

    /**
     * Has this section just been created?
     * @return {Boolean}
     */
    isNew() {
      return (
        this.savedSection.raw_created_at === this.savedSection.raw_updated_at
      );
    },

    /**
     * We only allow deletion if there are multiple sections.
     *
     * @return {Boolean}
     */
    canDelete() {
      return this.sectionCount > 1;
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
          core_relationship_id: participant.core_relationship.id,
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
     * Update the displayed participants.
     * @param {Array} checkedParticipants List of new participants
     */
    updateDisplayedParticipants(checkedParticipants) {
      this.displayedParticipants = this.displayedParticipants.concat(
        checkedParticipants.map(participant => {
          return {
            can_view: false,
            core_relationship: participant,
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
        return value.core_relationship.id === participant.core_relationship.id
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
        value => value.core_relationship.id !== participant.core_relationship.id
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
      this.title = this.savedSection.title;
      this.disableEditing();
    },

    /**
     * Save section changes.
     */
    async trySave() {
      this.isSaving = true;

      try {
        const savedSection = await this.save();
        this.updateTitle(savedSection.section.title);
        this.updateSection(savedSection);
        this.$emit('mutation-success');
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
        mutation: UpdateSectionSettingsMutation,
        variables: {
          input: {
            section_id: this.section.id,
            title: this.title,
            relationships: this.getSectionRelationships(),
          },
        },
        refetchAll: false, // Don't refetch all the data again
      });

      const result = resultData.mod_perform_update_section_settings;
      this.savedSection = result.section;
      return result;
    },

    /**
     * Display the modal for confirming the deletion of the section.
     */
    showDeleteModal() {
      this.deleteSectionModalOpen = true;
    },

    /**
     * close the section delete modal
     */
    closeDeleteSectionModal() {
      this.deleteSectionModalOpen = false;
      this.deleting = false;
    },

    /**
     * delete a section
     */
    async deleteSection() {
      this.deleting = true;
      try {
        await this.$apollo.mutate({
          mutation: DeleteSectionMutation,
          variables: {
            input: {
              section_id: this.section.id,
            },
          },
        });
        this.$emit('delete-section');
        this.$emit('mutation-success');
      } catch (e) {
        this.$emit('mutation-error', e);
      }

      this.closeDeleteSectionModal();
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
      "edit_section",
      "modal_section_delete_message",
      "modal_section_delete_title",
      "no_participants_added",
      "section_action_add_above",
      "section_action_add_below",
      "section_action_delete",
      "section_dropdown_menu",
      "section_title",
      "unsaved_changes_warning",
      "untitled_section"
    ],
    "moodle": [
      "cancel",
      "delete"
    ]
  }
</lang-strings>
