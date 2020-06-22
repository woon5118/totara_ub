<template>
  <div class="tui-performManageActivityAssignmentsForm">
    <Grid
      class="tui-performManageActivityAssignmentsForm__heading"
      :stack-at="600"
      :use-vertical-gap="false"
    >
      <GridItem grows>
        <h3 class="tui-performManageActivityAssignmentsForm__heading-title">
          {{ $str('user_group_assignment_title', 'mod_perform') }}
        </h3>
      </GridItem>
      <GridItem
        grows
        :units="1"
        class="tui-performManageActivityAssignmentsForm__heading-buttons"
      >
        <Dropdown
          class="tui-performManageActivityAssignmentsForm__heading-dropdown"
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
        </Dropdown>
      </GridItem>
    </Grid>

    <Table
      class="tui-performManageActivityAssignmentsForm__table"
      :data="assignments.length > 0 ? assignments : noAssignments"
    >
      <template v-slot:header-row>
        <HeaderCell size="8" valign="center">
          {{ $str('user_group_assignment_name', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="4" valign="center">
          {{ $str('user_group_assignment_type', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center">
          {{ $str('user_group_assignment_usercount', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center" />
      </template>
      <template v-slot:row="{ row }">
        <template>
          <Cell
            size="8"
            valign="center"
            :column-header="$str('user_group_assignment_name', 'mod_perform')"
          >
            {{ row.group.name }}
          </Cell>
          <Cell
            size="4"
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
            <span v-if="row.group.id">TBD</span>
          </Cell>
          <Cell size="2" valign="center" align="end">
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
    <div class="tui-performManageActivityAssignmentsForm__summary">
      <Schedule
        v-if="track"
        :track="track"
        :dynamic-date-sources="dynamicDateSources"
        :default-fixed-date="defaultFixedDateSetting"
        :activity-id="activityId"
      />
    </div>
    <AudienceAdder
      :open="isAdderOpen"
      :existing-items="addedIds"
      @added="updateSelectionFromAdder"
      @cancel="closeAdder"
    />

    <ConfirmationModal
      :title="$str('user_group_assignment_confirm_remove_title', 'mod_perform')"
      :open="confirmationModalOpen"
      @confirm="removeAssignment"
      @cancel="hideRemoveConfirmationModal"
    >
      <p>
        {{ $str('user_group_assignment_confirm_remove', 'mod_perform') }}
      </p>
    </ConfirmationModal>
  </div>
</template>

<script>
import AudienceAdder from 'totara_core/components/adder/AudienceAdder';
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Cell from 'totara_core/components/datatable/Cell';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import DeleteIcon from 'totara_core/components/icons/common/Delete';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Table from 'totara_core/components/datatable/Table';
import Schedule from 'mod_perform/components/manage_activity/assignment/Schedule';

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
    Grid,
    GridItem,
    HeaderCell,
    Table,
    Schedule,
  },

  props: {
    activityId: {
      type: Number,
      required: true,
    },
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
      addedIds: [],
      isAdderOpen: false,
      confirmationModalOpen: false,
      assignmentToRemove: null,
      adminEnum: 1,
      cohortEnum: 1,
    };
  },

  computed: {
    /**
     * Get the position of the dropdown menu based upon the viewport size.
     */
    dropdownPosition() {
      return screen.width > 600 ? 'bottom-right' : 'bottom-left';
    },
  },

  watch: {
    /**
     * Extracts the assignments from the associated track in this page.
     */
    track() {
      if (this.track) {
        this.assignments = this.track.assignments;
        this.addedIds = this.assignments
          .filter(assignment => assignment.group.type === this.cohortEnum)
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
      this.isAdderOpen = true;
    },

    /**
     * Hides the cohort selection dialog without any selections.
     */
    closeAdder() {
      this.isAdderOpen = false;
    },

    /**
     * Saves the assigned cohorts in the repository.
     */
    updateSelectionFromAdder(selection) {
      // Filter out previously added.
      const groups = selection.data
        .filter(item => this.addedIds.indexOf(item.id) == -1)
        .map(item => {
          return { id: item.id, type: this.cohortEnum };
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

      this.addedIds = selection.ids;
      this.isAdderOpen = false;
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
      "user_group_assignment_confirm_remove",
      "user_group_assignment_confirm_remove_title",
      "user_group_assignment_group_cohort",
      "user_group_assignment_name",
      "user_group_assignment_no_users",
      "user_group_assignment_title",
      "user_group_assignment_type",
      "user_group_assignment_unique_user_count_title",
      "user_group_assignment_unique_user_count_link",
      "user_group_assignment_usercount"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
