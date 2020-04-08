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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyDetailAssignment">
    <div class="tui-competencyDetailAssignment__bar">
      <Grid :stack-at="700">
        <GridItem :units="4">
          <!-- Competency assignment select list -->
          <SelectFilter
            v-model="selectedAssignment"
            class="tui-competencyDetailAssignment__bar-filter"
            :label="$str('assignment', 'totara_competency')"
            :name="'select_assignment'"
            :large="true"
            :options="activeAssignmentList"
            @input="selectedAssignmentChange"
          />
        </GridItem>
        <GridItem :units="4" :class="'tui-competencyDetailAssignment__level'">
          <div
            class="tui-competencyDetailAssignment__level-wrap"
            :class="
              'tui-competencyDetailAssignment__level-wrap-' +
                selectedAssignmentProficiencyState
            "
          >
            <p class="tui-competencyDetailAssignment__level-header">
              {{ $str('achievement_level', 'totara_competency') }}
              <!--
              This will be implemented in a later ticket, once a string has been decided on
              <InfoIconButton
                :aria-label="$str('more_information', 'totara_competency')"
                :class="'tui-competencyDetailAssignment__level-infoBtn'"
              >
                ...
              </InfoIconButton> -->
            </p>
            <div class="tui-competencyDetailAssignment__level-text">
              {{ selectedAssignmentProficiency.name }}
            </div>
          </div>
        </GridItem>
        <GridItem :units="4" :class="'tui-competencyDetailAssignment__status'">
          <ProgressTrackerCircle
            :state="selectedAssignmentProficiencyState"
            :target="selectedAssignmentProficiencyState !== 'complete'"
          />

          <span
            class="tui-competencyDetailAssignment__status-text"
            :class="{
              'tui-competencyDetailAssignment__status-text-complete':
                selectedAssignmentProficiencyState === 'complete',
            }"
          >
            {{
              $str(
                selectedAssignmentProficiency.proficient
                  ? 'proficient'
                  : 'not_proficient',
                'totara_competency'
              )
            }}
          </span>
        </GridItem>
      </Grid>
      <ConfirmationModal
        :title="$str('action_archive_user_modal_header', 'totara_competency')"
        :open="showArchiveConfirmation"
        @confirm="makeArchiveAssignmentMutation"
        @cancel="showArchiveConfirmation = false"
      >
        <p>
          {{
            $str('action_archive_user_assignment_modal', 'totara_competency')
          }}
        </p>
        <p>
          {{ $str('confirm_generic', 'totara_competency') }}
        </p>
      </ConfirmationModal>
    </div>
    <div class="tui-competencyDetailAssignment__actions">
      <ButtonIcon
        v-if="showCanArchiveButton"
        aria-label=""
        :styleclass="{ small: true }"
        :text="$str('action_archive_this', 'totara_competency')"
        @click="showArchiveConfirmDialog"
      >
        <ArchiveIcon />
      </ButtonIcon>
    </div>
  </div>
</template>

<script>
// Components
import ArchiveIcon from 'totara_core/components/icons/common/Archive';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import ProgressTrackerCircle from 'totara_core/components/progresstracker/ProgressTrackerCircle';
import SelectFilter from 'totara_core/components/filters/SelectFilter';
import { notify } from 'totara_core/notifications';
// GraphQL
import ArchiveUserAssignment from 'totara_competency/graphql/archive_user_assignment.graphql';
import CompetencyProfileDetailsQuery from 'totara_competency/graphql/profile_competency_details';

export default {
  components: {
    ArchiveIcon,
    ButtonIcon,
    ConfirmationModal,
    Grid,
    GridItem,
    ProgressTrackerCircle,
    SelectFilter,
  },

  props: {
    activeAssignmentList: {
      required: true,
      type: Array,
    },
    selectedAssignmentProficiency: {
      type: Object,
    },
    value: {
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      selectedAssignment: this.value,
      showArchiveConfirmation: false,
    };
  },

  computed: {
    /**
     * Return proficient state (pending, complete, achieved)
     *
     * @return {String}
     */
    selectedAssignmentProficiencyState() {
      if (
        this.selectedAssignmentProficiency.id &&
        this.selectedAssignmentProficiency.proficient
      ) {
        return 'achieved';
      } else if (this.selectedAssignmentProficiency.id) {
        return 'complete';
      } else {
        return 'pending';
      }
    },
    showCanArchiveButton() {
      return (
        this.activeAssignmentList[this.selectedAssignment] &&
        this.activeAssignmentList[this.selectedAssignment].can_archive
      );
    },
  },

  methods: {
    selectedAssignmentChange(e) {
      this.$emit('input', e);
    },
    showArchiveConfirmDialog() {
      this.showArchiveConfirmation = true;
    },
    makeArchiveAssignmentMutation() {
      let { assignment_id } = this.activeAssignmentList.find(
        assignment => assignment.id === this.value
      );

      this.$apollo
        .mutate({
          mutation: ArchiveUserAssignment,
          variables: {
            assignment_id: assignment_id,
          },
          refetchQueries: [
            {
              query: CompetencyProfileDetailsQuery,
              variables: {
                user_id: this.userId,
                competency_id: this.competencyId,
              },
            },
          ],
          refetchAll: false,
        })
        .then(() => {
          notify({
            type: 'success',
            message: this.$str(
              'event_assignment_archived',
              'totara_competency'
            ),
          });
          this.selectedAssignment = 0;
        })
        .catch(error => {
          let hasErrorMessage =
            error &&
            error.networkError &&
            error.networkError.result &&
            error.networkError.result.error;
          let errorMessage = this.$str(
            'error_generic_mutation',
            'totara_competency'
          );

          if (hasErrorMessage) {
            errorMessage = error.networkError.result.error;
          }
          notify({ type: 'error', message: errorMessage });
        })
        .finally(() => {
          this.showArchiveConfirmation = false;
        });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "achievement_level",
      "action_archive_this",
      "action_archive_user_modal_header",
      "action_archive_user_assignment_modal",
      "assignment",
      "confirm_generic",
      "error_generic_mutation",
      "event_assignment_archived",
      "more_information",
      "not_proficient",
      "proficient"
    ]
  }
</lang-strings>
