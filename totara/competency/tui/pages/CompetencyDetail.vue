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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyDetail">
    <!-- Back Link -->
    <div class="tui-competencyDetail__backLink">
      <a :href="goBackLink">
        {{ goBackText }}
      </a>
    </div>

    <!-- Title -->
    <h2 class="tui-competencyDetail__title">
      {{ data.competency.fullname }}
    </h2>

    <!-- Competency description & archived / activity log button -->
    <Grid :stack-at="700">
      <GridItem :units="7">
        <div class="tui-competencyDetail__description">
          {{ data.competency.description }}
        </div>
      </GridItem>
      <GridItem :units="5" :class="'tui-competencyDetail__buttons'">
        <Button
          :text="$str('archived_assignments', 'totara_competency')"
          :styleclass="{ small: true }"
          @click="openArchivedAssignmentModal"
        />

        <Button
          :text="$str('activity_log', 'totara_competency')"
          :styleclass="{ small: true }"
          @click="openActivityLogModal"
        />
      </GridItem>
    </Grid>

    <div class="tui-competencyDetail__body">
      <h3 class="tui-competencyDetail__body-title">
        {{ $str('current_assignment_details', 'totara_competency') }}
      </h3>

      <Loader :loading="$apollo.loading">
        <!-- No active assignment, can happen when all assignments are archived -->
        <div
          v-if="!activeAssignmentList.length"
          class="tui-competencyDetail__body-empty"
        >
          {{ $str('no_active_assignements', 'totara_competency') }}
        </div>

        <div v-else>
          <!-- Assignment selector & overview -->
          <Assignment
            v-model="assignmentFilter"
            :active-assignment-list="activeAssignmentList"
            :selected-assignment-proficiency="selectedAssignmentProficiency"
          />

          <!-- Progress overview -->
          <Progress
            :competency-id="competencyId"
            :my-value="selectedAssignmentProficiencyValue"
          />

          <!-- Scale achievement details -->
          <Achievements
            :user-id="userId"
            :assignment="selectedAssignmentData"
          />
        </div>
      </Loader>
    </div>

    <!-- Activity log modal -->
    <ModalPresenter
      :open="activityLogModalOpen"
      @request-close="closeActivityLogModal"
    >
      <Modal size="sheet">
        <ActivityLog :competency-id="competencyId" :user-id="userId" />
      </Modal>
    </ModalPresenter>

    <!-- Archived assignments modal -->
    <ModalPresenter
      :open="archivedAssignmentModalOpen"
      @request-close="closeArchivedAssignmentModal"
    >
      <Modal size="sheet">
        <ArchivedAssignments :competency-id="competencyId" :user-id="userId" />
      </Modal>
    </ModalPresenter>
  </div>
</template>

<script>
// Components
import Achievements from 'totara_competency/components/achievements/Achievements';
import ActivityLog from 'totara_competency/components/details/ActivityLog';
import ArchivedAssignments from 'totara_competency/components/details/ArchivedAssignments';
import Assignment from 'totara_competency/components/details/Assignment';
import Button from 'totara_core/components/buttons/Button';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Loader from 'totara_core/components/loader/Loader';
import Modal from 'totara_core/components/modal/Modal';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Progress from 'totara_competency/components/details/Progress';
// GraphQL
import CompetencyDetailsQuery from 'totara_competency/graphql/competency_details';

export default {
  components: {
    Achievements,
    ActivityLog,
    ArchivedAssignments,
    Assignment,
    Button,
    Grid,
    GridItem,
    Loader,
    Modal,
    ModalPresenter,
    Progress,
  },

  props: {
    competencyId: {
      required: true,
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
    goBackText: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      activityLogModalOpen: false,
      archivedAssignmentModalOpen: false,
      assignmentFilter: 0,
      data: {
        competency: {
          fullname: '',
        },
        items: [],
      },
    };
  },

  apollo: {
    data: {
      query: CompetencyDetailsQuery,
      variables() {
        return {
          user_id: this.userId,
          competency_id: this.competencyId,
        };
      },
      update({
        totara_competency_profile_competency_details: { competency, items },
      }) {
        return { competency, items };
      },
    },
  },

  computed: {
    /**
     * Check for selected assignment based on user selection & return if found
     *
     * @return {Object}
     */
    selectedAssignment() {
      if (this.data.items.length && this.data.items[this.assignmentFilter]) {
        return this.data.items[this.assignmentFilter];
      }
      return null;
    },

    /**
     * Return select assignment data
     *
     * @return {Object}
     */
    selectedAssignmentData() {
      if (this.selectedAssignment) {
        return this.selectedAssignment.assignment;
      }
      return {};
    },

    /**
     * Return selected assignment proficeny value data
     *
     * @return {Object}
     */
    selectedAssignmentProficiency() {
      if (this.selectedAssignment && this.selectedAssignment.my_value) {
        return this.selectedAssignment.my_value;
      }
      return {};
    },

    /**
     * Return selected assignment proficeny value ID
     *
     * @return {Int}
     */
    selectedAssignmentProficiencyValue() {
      if (
        this.selectedAssignmentProficiency &&
        this.selectedAssignmentProficiency.id
      ) {
        return this.selectedAssignmentProficiency.id;
      }
      return NaN;
    },

    /**
     * Create an array of active assignments to be used for the filter
     *
     * @return {Array}
     */
    activeAssignmentList() {
      let activeAssignmentList = [];

      // Collect array index & label for each assignment and filter out archived
      activeAssignmentList = this.data.items
        .map(function(elem, index) {
          return {
            archived:
              elem.assignment.archived_at || elem.assignment.type === 'legacy',
            id: index,
            label: elem.assignment.progress_name,
          };
        })
        .filter(function(assignment) {
          return !assignment.archived;
        });

      return activeAssignmentList;
    },
  },

  methods: {
    /**
     * Open activity log modal
     */
    openActivityLogModal() {
      this.activityLogModalOpen = true;
    },

    /**
     * Open archived assignment modal
     */
    openArchivedAssignmentModal() {
      this.archivedAssignmentModalOpen = true;
    },

    /**
     * Close activity log modal
     */
    closeActivityLogModal() {
      this.activityLogModalOpen = false;
    },

    /**
     * Close archived assignment modal
     */
    closeArchivedAssignmentModal() {
      this.archivedAssignmentModalOpen = false;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "activity_log",
      "archived_assignments",
      "current_assignment_details",
      "no_active_assignements"
    ]
  }
</lang-strings>
