<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performManageActivityAssignmentsForm">
    <h3 class="tui-performManageActivityAssignmentsForm__heading">
      {{ $str('user_group_assignment_title', 'mod_perform') }}
    </h3>

    <!-- Drop down for adding groups -->
    <div class="tui-performManageActivityAssignmentsForm__add">
      <Dropdown
        class="tui-performManageActivityAssignmentsForm__add-dropdown"
        :separator="true"
        :position="dropdownPosition"
      >
        <template v-slot:trigger="{ toggle, isOpen }">
          <Button
            :aria-expanded="isOpen ? 'true' : 'false'"
            :text="$str('user_group_assignment_add_group', 'mod_perform')"
            :caret="true"
            :styleclass="{
              primary: true,
            }"
            @click="toggle"
          />
        </template>
        <DropdownItem @click="openAdder">
          {{ $str('user_group_assignment_group_cohort', 'mod_perform') }}
        </DropdownItem>
        <DropdownItem @click="openOrgAdder">
          {{ $str('user_group_assignment_group_organisation', 'mod_perform') }}
        </DropdownItem>
        <DropdownItem @click="openPosAdder">
          {{ $str('user_group_assignment_group_position', 'mod_perform') }}
        </DropdownItem>
      </Dropdown>
    </div>

    <AudienceAdder
      :open="isAudienceAdderOpen"
      :existing-items="audienceAddedIds"
      :context-id="activityContextId"
      @added="
        selection =>
          updateSelectionFromAdder(selection, audienceAddedIds, cohortEnum)
      "
      @cancel="closeAdder"
    />

    <OrganisationAdder
      :open="isOrgAdderOpen"
      :existing-items="orgAddedIds"
      @added="
        selection => updateSelectionFromAdder(selection, orgAddedIds, orgEnum)
      "
      @cancel="closeOrgAdder"
    />

    <PositionAdder
      :open="isPosAdderOpen"
      :existing-items="posAddedIds"
      @added="
        selection => updateSelectionFromAdder(selection, posAddedIds, posEnum)
      "
      @cancel="closePosAdder"
    />

    <Table
      class="tui-performManageActivityAssignmentsForm__table"
      :data="assignments.length > 0 ? assignments : noAssignments"
    >
      <template v-slot:header-row>
        <HeaderCell size="11" valign="center">
          {{ $str('user_group_assignment_name', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center">
          {{ $str('user_group_assignment_type', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center">
          {{ $str('user_group_assignment_usercount', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="1" valign="center" />
      </template>
      <template v-slot:row="{ row }">
        <template>
          <Cell
            size="11"
            valign="center"
            :column-header="$str('user_group_assignment_name', 'mod_perform')"
          >
            {{ row.group.name }}
          </Cell>
          <Cell
            size="2"
            valign="center"
            :column-header="$str('user_group_assignment_type', 'mod_perform')"
          >
            {{ row.group.type_label }}
          </Cell>
          <Cell
            size="2"
            valign="center"
            :column-header="
              $str('user_group_assignment_usercount', 'mod_perform')
            "
          >
            {{ row.group.size }}
          </Cell>
          <Cell
            size="1"
            valign="center"
            align="end"
            :column-header="$str('view_actions', 'mod_perform')"
          >
            <ButtonIcon
              v-if="row.group.id"
              :aria-label="$str('delete')"
              :styleclass="{
                small: true,
                transparent: true,
              }"
              @click="
                showRemoveConfirmationModal(
                  row.type,
                  row.group.id,
                  row.group.type
                )
              "
            >
              <DeleteIcon />
            </ButtonIcon>
          </Cell>
        </template>
      </template>
    </Table>

    <Schedule
      v-if="track"
      :track="track"
      :dynamic-date-sources="dynamicDateSources"
      :default-fixed-date="defaultFixedDateSetting"
      :activity-id="activityId"
      :activity-state="activityState"
    />

    <ConfirmationModal
      :title="$str('user_group_assignment_confirm_remove_title', 'mod_perform')"
      :confirm-button-text="
        $str('user_group_assignment_confirm_modal_remove', 'mod_perform')
      "
      :open="confirmationModalOpen"
      @confirm="removeAssignment"
      @cancel="hideRemoveConfirmationModal"
    >
      <p v-if="isActive">
        {{ $str('user_group_assignment_confirm_remove_active', 'mod_perform') }}
      </p>
      <p v-else>
        {{ $str('user_group_assignment_confirm_remove_draft', 'mod_perform') }}
      </p>
    </ConfirmationModal>
  </div>
</template>

<script>
import AudienceAdder from 'tui/components/adder/AudienceAdder';
import Button from 'tui/components/buttons/Button';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Cell from 'tui/components/datatable/Cell';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import DeleteIcon from 'tui/components/icons/common/Delete';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import OrganisationAdder from 'tui/components/adder/OrganisationAdder';
import PositionAdder from 'tui/components/adder/PositionAdder';
import Table from 'tui/components/datatable/Table';
import Schedule from 'mod_perform/components/manage_activity/assignment/Schedule';
import { ACTIVITY_STATUS_ACTIVE } from 'mod_perform/constants';

//GraphQL
import TrackSettingsQuery from 'mod_perform/graphql/default_track_settings';
import AddTrackAssignmentMutation from 'mod_perform/graphql/add_track_assignments';
import RemoveTrackAssignmentMutation from 'mod_perform/graphql/remove_track_assignments';

export default {
  components: {
    AudienceAdder,
    Button,
    ButtonIcon,
    Cell,
    ConfirmationModal,
    DeleteIcon,
    Dropdown,
    DropdownItem,
    HeaderCell,
    Table,
    Schedule,
    OrganisationAdder,
    PositionAdder,
  },

  props: {
    activityId: {
      type: Number,
      required: true,
    },
    activityState: {
      type: String,
      required: true,
    },
    activityContextId: Number,
  },

  data() {
    return {
      track: null,
      dynamicDateSources: [],
      defaultFixedDateSetting: null,
      trackSettings: null,
      assignments: [],
      noAssignments: [
        {
          group: {
            name: this.$str('user_group_assignment_no_users', 'mod_perform'),
          },
        },
      ],
      audienceAddedIds: [],
      orgAddedIds: [],
      posAddedIds: [],
      isAudienceAdderOpen: false,
      isOrgAdderOpen: false,
      isPosAdderOpen: false,
      confirmationModalOpen: false,
      assignmentToRemove: null,
      adminEnum: 1,
      cohortEnum: 1,
      orgEnum: 2,
      posEnum: 3,
    };
  },

  computed: {
    /**
     * Get the position of the dropdown menu based upon the viewport size.
     */
    dropdownPosition() {
      return screen.width > 600 ? 'bottom-right' : 'bottom-left';
    },
    isActive() {
      return this.activityState == ACTIVITY_STATUS_ACTIVE;
    },
  },

  watch: {
    /**
     * Extracts the assignments from the associated track in this page.
     */
    track() {
      if (this.track) {
        this.assignments = this.track.assignments;
        this.audienceAddedIds = this.assignments
          .filter(assignment => assignment.group.type === this.cohortEnum)
          .map(assignment => assignment.group.id);

        this.orgAddedIds = this.assignments
          .filter(assignment => assignment.group.type === this.orgEnum)
          .map(assignment => assignment.group.id);

        this.posAddedIds = this.assignments
          .filter(assignment => assignment.group.type === this.posEnum)
          .map(assignment => assignment.group.id);
      }
      this.assignments;
    },

    /**
     * Used so we can get track, date resolver options, and default fixed date setting in one query.
     */
    trackSettings(newValue) {
      this.track = newValue.track;
      this.defaultFixedDateSetting = newValue.defaultFixedDateSetting;

      if (
        this.track.schedule_dynamic_source &&
        this.track.schedule_dynamic_source.is_available === false
      ) {
        this.dynamicDateSources = this.addedDeletedDateSourceToList(
          newValue.dynamicDateSources
        );
      } else {
        this.dynamicDateSources = newValue.dynamicDateSources;
      }
    },
  },

  methods: {
    /**
     * Shows the cohort selection dialog.
     */
    openAdder() {
      this.isAudienceAdderOpen = true;
    },

    /**
     * Hides the cohort selection dialog without any selections.
     */
    closeAdder() {
      this.isAudienceAdderOpen = false;
    },

    openOrgAdder() {
      this.isOrgAdderOpen = true;
    },

    closeOrgAdder() {
      this.isOrgAdderOpen = false;
    },

    openPosAdder() {
      this.isPosAdderOpen = true;
    },

    closePosAdder() {
      this.isPosAdderOpen = false;
    },

    /**
     * Saves the assigned cohorts in the repository.
     */
    updateSelectionFromAdder(selection, addedIds, adderType) {
      // Filter out previously added.
      const groups = selection.data
        .filter(item => addedIds.indexOf(item.id) == -1)
        .map(item => {
          return { id: item.id, type: adderType };
        });

      const selected = {
        track_id: this.track.id,
        type: this.adminEnum,
        groups: groups,
      };

      this.updateAssignmentsInRepository(
        AddTrackAssignmentMutation,
        'mod_perform_add_track_assignments',
        selected
      );

      this.$_postProcess(adderType, selection);
    },

    $_postProcess(adderType, selection) {
      if (adderType === this.cohortEnum) {
        this.audienceAddedIds = selection.ids;
        this.isAudienceAdderOpen = false;
      }
      if (adderType === this.orgEnum) {
        this.orgAddedIds = selection.ids;
        this.isOrgAdderOpen = false;
      }

      if (adderType === this.posEnum) {
        this.posAddedIds = selection.ids;
        this.isPosAdderOpen = false;
      }
    },

    /**
     * Removes the assigned user groupings from the repository.
     */
    removeAssignment() {
      if (!this.assignmentToRemove) {
        return;
      }

      const toBeRemoved = {
        track_id: this.track.id,
        type: this.assignmentToRemove.assignmentType,
        groups: [
          {
            id: this.assignmentToRemove.groupId,
            type: this.assignmentToRemove.groupType,
          },
        ],
      };

      this.updateAssignmentsInRepository(
        RemoveTrackAssignmentMutation,
        'mod_perform_remove_track_assignments',
        toBeRemoved
      );
    },

    /**
     * Add the currently selected date resolver to the front of the selections,
     * with a modified "deleted" label.
     */
    addedDeletedDateSourceToList(dynamicDateSources) {
      const deletedDisplayName = this.$str(
        'deleted_dynamic_source_label',
        'mod_perform',
        this.track.schedule_dynamic_source.display_name
      );

      const deletedOption = Object.assign(
        {},
        this.track.schedule_dynamic_source,
        { display_name: deletedDisplayName }
      );

      const options = [deletedOption];
      options.push.apply(options, dynamicDateSources);

      return options;
    },

    /**
     * Convenience function to execute a graphql mutation.
     */
    async updateAssignmentsInRepository(mutation, mutationName, assignments) {
      const variables = {
        assignments: assignments,
      };

      try {
        const { data: result } = await this.$apollo.mutate({
          mutation,
          variables,
          refetchAll: false, // Don't refetch all the data again
        });

        const savedTrack = result[mutationName];

        this.hideRemoveConfirmationModal();

        if (savedTrack) {
          this.track = savedTrack;
          this.$emit('mutation-success');
        } else {
          this.$emit('mutation-error');
        }
      } catch (e) {
        console.log('update track assignments error', e);
        this.$emit('mutation-error');
      }
    },

    /**
     * Shows the remove assignment confirmation dialog.
     */
    showRemoveConfirmationModal(assignmentType, groupId, groupType) {
      this.assignmentToRemove = {
        assignmentType: assignmentType,
        groupId: groupId,
        groupType: groupType,
      };
      this.confirmationModalOpen = true;
    },

    /**
     * Hides the remove assignment confirmation dialog.
     */
    hideRemoveConfirmationModal() {
      this.assignmentToRemove = null;
      this.confirmationModalOpen = false;
    },
  },

  apollo: {
    trackSettings: {
      query: TrackSettingsQuery,
      fetchPolicy: 'network-only', // Always refetch data on tab change
      variables() {
        return {
          activity_id: this.activityId,
        };
      },
      update: data => {
        return {
          track: data.mod_perform_default_track,
          dynamicDateSources: data.mod_perform_available_dynamic_date_sources,
          defaultFixedDateSetting: data.mod_perform_default_fixed_date_setting,
        };
      },
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform" : [
      "deleted_dynamic_source_label",
      "user_group_assignment_add_group",
      "user_group_assignment_confirm_modal_remove",
      "user_group_assignment_confirm_remove_active",
      "user_group_assignment_confirm_remove_draft",
      "user_group_assignment_confirm_remove_title",
      "user_group_assignment_group_cohort",
      "user_group_assignment_group_organisation",
      "user_group_assignment_group_position",
      "user_group_assignment_name",
      "user_group_assignment_no_users",
      "user_group_assignment_title",
      "user_group_assignment_type",
      "user_group_assignment_unique_user_count_title",
      "user_group_assignment_unique_user_count_link",
      "user_group_assignment_usercount",
      "view_actions"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
