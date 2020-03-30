<template>
  <div class="tui-performManageActivityAssignmentsForm">
    <Grid class="tui-performManageActivityAssignmentsForm__heading">
      <GridItem :grows="true">
        <h3 class="tui-performManageActivityAssignmentsForm__heading-title">
          {{ $str('perform:user_group_assignment:title', 'mod_perform') }}
        </h3>
      </GridItem>
      <GridItem
        :units="2"
        :class="'tui-performManageActivityAssignmentsForm__heading-buttons'"
      >
        <Dropdown
          :class="'tui-performManageActivityAssignmentsForm__heading-dropdown'"
          :separator="true"
          position="bottom-right"
        >
          <template v-slot:trigger="{ toggle, isOpen }">
            <Button
              :aria-expanded="isOpen ? 'true' : 'false'"
              :text="
                $str('perform:user_group_assignment:add:group', 'mod_perform')
              "
              :caret="true"
              :styleclass="{
                primary: true,
              }"
              @click="toggle"
            />
          </template>
          <DropdownItem @click="showCohortModal = true">
            {{
              $str('perform:user_group_assignment:group:cohort', 'mod_perform')
            }}
          </DropdownItem>
        </Dropdown>
      </GridItem>
    </Grid>

    <Table
      :class="'tui-performManageActivityAssignmentsForm__table'"
      :data="assignments.length > 0 ? assignments : noAssignments"
    >
      <template v-slot:header-row>
        <HeaderCell size="8" valign="center">
          {{ $str('perform:user_group_assignment:name', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="4" valign="center">
          {{ $str('perform:user_group_assignment:type', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center">
          {{ $str('perform:user_group_assignment:usercount', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2" valign="center" />
      </template>
      <template v-slot:row="{ row }">
        <template>
          <Cell
            size="8"
            valign="center"
            :column-header="
              $str('perform:user_group_assignment:name', 'mod_perform')
            "
          >
            {{ row.group.name }}
          </Cell>
          <Cell
            size="4"
            valign="center"
            :column-header="
              $str('perform:user_group_assignment:type', 'mod_perform')
            "
          >
            {{ row.group.type_label }}
          </Cell>
          <Cell
            size="2"
            valign="center"
            :column-header="
              $str('perform:user_group_assignment:usercount', 'mod_perform')
            "
          >
            <span v-if="row.group.id">TBD</span>
          </Cell>
          <Cell size="2" valign="center" align="end">
            <ButtonIcon
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
              <DeleteIcon v-if="row.group.id" />
            </ButtonIcon>
          </Cell>
        </template>
      </template>
    </Table>

    <div>
      <h3>
        {{
          $str(
            'perform:user_group_assignment:unique_user_count:title',
            'mod_perform'
          )
        }}
      </h3>

      <div class="tui-performManageActivityAssignmentsForm__summary">
        <span class="tui-performManageActivityAssignmentsForm__summary-count">
          <span>0</span>
        </span>
        <a href="#TBC">{{
          $str(
            'perform:user_group_assignment:unique_user_count:link',
            'mod_perform'
          )
        }}</a>
      </div>
    </div>

    <ModalPresenter
      :open="showCohortModal"
      @request-close="updateCohortSelection"
    >
      <SelectCohortModal :assigned="getAlreadyAssigned('cohort')" />
    </ModalPresenter>

    <ConfirmationModal
      :title="
        $str(
          'perform:user_group_assignment:confirm:remove:title',
          'mod_perform'
        )
      "
      :open="confirmationModalOpen"
      @confirm="removeAssignment"
      @cancel="hideRemoveConfirmationModal"
    >
      <p>
        {{
          $str('perform:user_group_assignment:confirm:remove', 'mod_perform')
        }}
      </p>
    </ConfirmationModal>
  </div>
</template>

<script>
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
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import SelectCohortModal from 'mod_perform/components/manage_activity/SelectCohortModal';
import Table from 'totara_core/components/datatable/Table';

//GraphQL
import TrackQuery from 'mod_perform/graphql/default_track';
import AddTrackAssignmentMutation from 'mod_perform/graphql/add_track_assignments';
import RemoveTrackAssignmentMutation from 'mod_perform/graphql/remove_track_assignments';

export default {
  components: {
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
    ModalPresenter,
    SelectCohortModal,
    Table,
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
      assignments: [],
      noAssignments: [
        {
          group: {
            name: this.$str(
              'perform:user_group_assignment:nousers',
              'mod_perform'
            ),
          },
        },
      ],
      showCohortModal: false,
      confirmationModalOpen: false,
      assignmentToRemove: null,
      adminEnum: 1,
      cohortEnum: 1,
    };
  },

  watch: {
    /**
     * Extracts the assignments from the associated track in this page.
     */
    track() {
      if (this.track) {
        this.assignments = this.track.assignments;
      }
      this.assignments;
    },
  },

  methods: {
    /**
     * Saves the assigned cohorts in the repository.
     */
    updateCohortSelection(items) {
      if (items.selected) {
        const selected = {
          track_id: this.track.id,
          type: this.adminEnum,
          groups: items.selected.map(id => {
            return { id: id, type: this.cohortEnum };
          }),
        };

        this.updateAssignmentsInRepository(
          AddTrackAssignmentMutation,
          'mod_perform_add_track_assignments',
          selected
        );
      }

      this.showCohortModal = false;
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

    /**
     * Filters out already assigned groups so that the selection dialog does not
     * show them.
     */
    getAlreadyAssigned() {
      return this.assignments
        .filter(assignment => assignment.group.type === this.cohortEnum)
        .map(assignment => assignment.group.id);
    },
  },

  apollo: {
    track: {
      query: TrackQuery,
      variables() {
        return {
          activity_id: this.activityId,
        };
      },
      update: data => data.mod_perform_default_track,
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform" : [
      "perform:user_group_assignment:add:group",
      "perform:user_group_assignment:confirm:remove",
      "perform:user_group_assignment:confirm:remove:title",
      "perform:user_group_assignment:group:cohort",
      "perform:user_group_assignment:name",
      "perform:user_group_assignment:nousers",
      "perform:user_group_assignment:title",
      "perform:user_group_assignment:type",
      "perform:user_group_assignment:unique_user_count:title",
      "perform:user_group_assignment:unique_user_count:link",
      "perform:user_group_assignment:usercount"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
