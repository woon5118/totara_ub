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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @module totara_competency
-->

<template>
  <Loader :loading="$apollo.loading">
    <div class="tui-competencyDetail">
      <template v-if="data.competency">
        <!-- User details, back link and page title -->
        <div class="tui-competencyDetail__header">
          <MiniProfileCard
            v-if="user && !isMine"
            :display="user.card_display"
          />
          <div>
            <a :href="goBackLink">
              {{ goBackText }}
            </a>
          </div>
          <PageHeading :title="data.competency.fullname" />
        </div>

        <!-- Competency description & archived / activity log button -->
        <Grid :stack-at="700">
          <GridItem :units="7">
            <div
              class="tui-competencyDetail__description"
              v-html="data.competency.description"
            />
          </GridItem>
          <GridItem :class="'tui-competencyDetail__buttons'" :units="5">
            <Button
              :styleclass="{ small: true }"
              :text="$str('archived_assignments', 'totara_competency')"
              @click="openArchivedAssignmentModal"
            />

            <Button
              :styleclass="{ small: true }"
              :text="$str('activity_log', 'totara_competency')"
              @click="openActivityLogModal"
            />
          </GridItem>
        </Grid>

        <div class="tui-competencyDetail__body">
          <h3 class="tui-competencyDetail__body-title">
            {{ $str('current_assignment_details', 'totara_competency') }}
          </h3>

          <!-- No active assignment, can happen when all assignments are archived -->
          <div
            v-if="!activeAssignmentList.length"
            class="tui-competencyDetail__body-empty"
          >
            {{ $str('no_active_assignments', 'totara_competency') }}
          </div>

          <div v-else>
            <!-- Assignment selector & overview -->
            <Assignment
              v-model="assignmentFilter"
              :active-assignment-list="activeAssignmentList"
              :selected-assignment-proficiency="selectedAssignmentProficiency"
              :competency-id="competencyId"
              :user-id="userId"
            />

            <!-- Progress overview -->
            <Progress
              :competency-id="competencyId"
              :my-value="selectedAssignmentProficiencyValue"
            />

            <!-- Scale achievement details -->
            <Achievements
              :assignment="selectedAssignmentData"
              :user-id="userId"
            />
          </div>
        </div>
      </template>

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
          <ArchivedAssignments
            :competency-id="competencyId"
            :user-id="userId"
          />
        </Modal>
      </ModalPresenter>
    </div>
  </Loader>
</template>

<script>
// Components
import Achievements from 'totara_competency/components/achievements/Achievements';
import ActivityLog from 'totara_competency/components/details/ActivityLog';
import ArchivedAssignments from 'totara_competency/components/details/ArchivedAssignments';
import Assignment from 'totara_competency/components/details/Assignment';
import Button from 'tui/components/buttons/Button';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loading/Loader';
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import Modal from 'tui/components/modal/Modal';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import PageHeading from 'tui/components/layouts/PageHeading';
import Progress from 'totara_competency/components/details/Progress';
import { notify } from 'tui/notifications';
// GraphQL
import CompetencyProfileDetailsQuery from 'totara_competency/graphql/profile_competency_details';
import UserQuery from 'totara_competency/graphql/user';

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
    MiniProfileCard,
    Modal,
    ModalPresenter,
    PageHeading,
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
    isMine: {
      required: true,
      type: Boolean,
    },
    showActivityLogByDefault: {
      default: false,
      type: Boolean,
    },
    toastMessage: {
      type: String,
    },
  },

  data() {
    return {
      activityLogModalOpen: false,
      archivedAssignmentModalOpen: false,
      assignmentFilter: 0,
      data: {
        competency: null,
        items: [],
      },
      user: null,
    };
  },

  apollo: {
    data: {
      query: CompetencyProfileDetailsQuery,
      variables() {
        return {
          user_id: this.userId,
          competency_id: this.competencyId,
        };
      },
      update({ totara_competency_profile_competency_details }) {
        const details = totara_competency_profile_competency_details;

        if (details === null) {
          return { competency: null, items: [] };
        }

        return { competency: details.competency, items: details.items };
      },
    },
    user: {
      query: UserQuery,
      variables() {
        return { user_id: this.userId };
      },
      update: data => data.totara_competency_user,
      skip() {
        return this.isMine;
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
     * Return selected assignment proficiency value data
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
     * Return selected assignment proficiency value ID
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
        .map((elem, index) => {
          return {
            archived:
              elem.assignment.archived_at || elem.assignment.type === 'legacy',
            id: index,
            assignment_id: elem.assignment.id,
            can_archive: elem.assignment.can_archive,
            label: this.getFilterLabel(elem),
          };
        })
        .filter(function(assignment) {
          return !assignment.archived;
        });

      if (activeAssignmentList.length) {
        this.updateAssignmentFilterValue(activeAssignmentList[0].id);
      }

      return activeAssignmentList;
    },
  },

  mounted() {
    if (this.toastMessage) {
      notify({ message: this.toastMessage });
    }
    this.activityLogModalOpen = this.showActivityLogByDefault;
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

    /**
     * Update default assignment filter value
     */
    updateAssignmentFilterValue(n) {
      this.assignmentFilter = n;
    },

    /**
     * Modify the assignment label if it was directly assigned
     *
     * @param {Object} entry
     * @return {String}
     */
    getFilterLabel(entry) {
      let label = entry.assignment.progress_name;
      // Special handling for direct assignments which we list
      // as "Directly assigned", this adds some more information
      // to the string which includes the full name of the assigner and their role
      if (this.isDirectlyAssigned(entry.assignment)) {
        let str_options = {
          progress_name: entry.assignment.progress_name,
          user_fullname_role: entry.assignment.reason_assigned,
        };
        label = this.$str(
          'progress_name_by_user',
          'totara_competency',
          str_options
        );
      }

      return label;
    },

    /**
     * Calculate if label was directly assigned
     *
     * @param {Object} assignment
     * @return {Boolean}
     */
    isDirectlyAssigned(assignment) {
      if (assignment.user_group_type !== 'user') {
        return false;
      }

      return assignment.type === 'admin' || assignment.type === 'other';
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
      "competency_does_not_exist",
      "no_active_assignments"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-competencyDetail {
  & > * + * {
    margin-top: var(--gap-2);
  }

  &__header {
    & > * + * {
      margin-top: var(--gap-2);
    }
  }

  &__body {
    margin-top: var(--gap-6);
    border-top: 1px solid var(--color-neutral-5);

    &-title {
      @include tui-font-heading-small();
    }

    &-empty {
      @include tui-font-hint();
    }
  }

  &__buttons {
    text-align: right;

    & > * + * {
      margin-top: var(--gap-4);
      margin-left: var(--gap-2);
    }
  }

  &__description {
    @include tui-font-body();
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-competencyDetail {
    &__buttons {
      > * {
        margin-top: 0;
      }
    }
  }
}
</style>
