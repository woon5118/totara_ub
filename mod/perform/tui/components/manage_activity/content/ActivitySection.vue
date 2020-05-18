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
    <Grid :stack-at="1200">
      <GridItem :units="6">
        <h3 class="mod-perform-activitySection__participant-heading">
          {{ $str('activity_participants_heading', 'mod_perform') }}
        </h3>
        <SectionRelationship
          v-for="participant in displayedParticipantsSorted"
          :key="participant.relationship.id"
          :participant="participant"
          @participant-removed="removeDisplayedParticipant"
          @can-view-changed="updateParticipantData"
        />
        <ParticipantsPopover
          :active-participants="displayedParticipants"
          @update-participants="updateDisplayedParticipants"
        />

        <br />
        <ButtonGroup>
          <Button
            :styleclass="{ primary: true }"
            :text="$str('activity_section_save_changes', 'mod_perform')"
            :disabled="isSaving || !hasChanges"
            @click="trySave"
          />
          <Button
            :styleclass="{ primary: false }"
            :text="$str('cancel')"
            :disabled="isSaving || !hasChanges"
            @click="resetSectionChanges"
          />
        </ButtonGroup>
      </GridItem>
    </Grid>
    <Grid>
      <GridItem grows :units="10">
        <Table
          :data="elementsSummary"
          :border-bottom-hidden="true"
          :border-top-hidden="true"
        >
          <template v-slot:header-row>
            <HeaderCell size="4">{{
              $str('section_element_summary_required_questions', 'mod_perform')
            }}</HeaderCell>
            <HeaderCell size="4">{{
              $str('section_element_summary_optional_questions', 'mod_perform')
            }}</HeaderCell>
            <HeaderCell size="4">{{
              $str(
                'section_element_summary_other_content_elements',
                'mod_perform'
              )
            }}</HeaderCell>
          </template>
          <template v-slot:row="{ row }">
            <Cell size="4">
              {{ row.required_question_count }}
            </Cell>
            <Cell size="4">
              {{ row.optional_question_count }}
            </Cell>
            <Cell size="4">
              {{ row.other_element_count }}
            </Cell>
          </template>
        </Table>
      </GridItem>
      <GridItem grows :units="1">
        <div class="mod-perform-activitySection__action-buttons">
          <Button
            :text="$str('edit_content_elements', 'mod_perform')"
            :styleclass="{ small: true }"
            @click="modelOpen = true"
          />
        </div>
      </GridItem>
    </Grid>
    <ModalPresenter :open="modelOpen" @request-close="modalRequestClose">
      <EditSectionContentModal
        :section-id="section.id"
        @mutation-success="showMutationSuccessNotification"
        @mutation-error="showMutationErrorNotification"
        @update-summary="updateSummary"
      />
    </ModalPresenter>
  </Card>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Card from 'totara_core/components/card/Card';
import EditSectionContentModal from 'mod_perform/components/manage_activity/content/EditSectionContentModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import ParticipantsPopover from 'mod_perform/components/manage_activity/content/ParticipantsPopover';
import SectionRelationship from 'mod_perform/components/manage_activity/content/SectionRelationship';
import UpdateSectionRelationshipsMutation from 'mod_perform/graphql/update_section_relationships.graphql';
import Table from 'totara_core/components/datatable/Table';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';

export default {
  components: {
    Button,
    ButtonGroup,
    Card,
    EditSectionContentModal,
    Grid,
    GridItem,
    ModalPresenter,
    ParticipantsPopover,
    SectionRelationship,
    Table,
    Cell,
    HeaderCell,
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
      savedSection: this.section,
      displayedParticipants: this.getParticipantsFromSection(this.section),
      isSaving: false,
      elementsSummary: [this.section.section_elements_summary],
    };
  },
  computed: {
    savedParticipants() {
      return this.getParticipantsFromSection(this.savedSection);
    },
    // Has anything changed compared to last saved state?
    // Checks for difference between displayed & last saved participants arrays.
    hasChanges: function() {
      return !(
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
    displayedParticipantsSorted() {
      return this.displayedParticipants
        .slice()
        .sort((a, b) => a.relationship.id - b.relationship.id);
    },
  },
  methods: {
    getParticipantsFromSection(section) {
      if (section.section_relationships) {
        return section.section_relationships;
      }
      return [];
    },

    getSectionRelationships() {
      return this.displayedParticipants.map(participant => {
        return {
          id: participant.relationship.id,
          can_view: participant.can_view,
        };
      });
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
        checkedParticipants.map(participant => {
          return {
            can_view: false,
            relationship: participant,
          };
        })
      );
    },
    updateParticipantData(participant) {
      this.displayedParticipants = this.displayedParticipants.map(value => {
        return value.relationship.id === participant.relationship.id
          ? participant
          : value;
      });
    },
    removeDisplayedParticipant(participant) {
      this.displayedParticipants = this.displayedParticipants.filter(
        value => value.relationship.id !== participant.relationship.id
      );
    },
    resetSectionChanges() {
      this.displayedParticipants = this.getParticipantsFromSection(
        this.savedSection
      );
    },

    showMutationSuccessNotification() {
      this.$emit('mutation-success');
    },

    showMutationErrorNotification() {
      this.$emit('mutation-error');
    },

    updateSummary(data) {
      this.elementsSummary = [data];
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
      "activity_participants_heading",
      "activity_section_save_changes",
      "edit_content_elements",
      "activity_participants_add",
      "activity_participants_heading",
      "activity_section_save_changes",
      "section_element_summary_required_questions",
      "section_element_summary_optional_questions",
      "section_element_summary_other_content_elements"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
