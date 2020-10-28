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
  <div class="tui-performUserActivityList">
    <ActivitiesFilter
      v-if="showFilter"
      :filter-options="filterOptions"
      :shown="subjectInstances.length"
      :total="pagination.total"
      @update-filters="applyFilters"
    />

    <Loader :loading="$apollo.loading">
      <Table
        v-if="!$apollo.loading"
        :data="subjectInstances"
        :no-items-text="emptyListText"
        :expandable-rows="true"
      >
        <template v-slot:header-row>
          <ExpandCell :header="true" />
          <HeaderCell :size="isAboutOthers ? '3' : '7'">
            {{ $str('user_activities_title_header', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell size="2">
            {{ $str('user_activities_type_header', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell v-if="isAboutOthers" size="2">
            {{ $str('user_activities_subject_header', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell v-if="hasSubjectInstanceWithJobAssignment" size="2">
            {{ $str('user_activities_job_assignment_header', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell v-if="isAboutOthers" size="2">
            {{
              $str('user_activities_status_header_relationship', 'mod_perform')
            }}
          </HeaderCell>
          <HeaderCell size="2">
            {{ $str('user_activities_status_header_activity', 'mod_perform') }}
          </HeaderCell>
          <HeaderCell size="2">
            {{
              $str('user_activities_status_header_participation', 'mod_perform')
            }}
          </HeaderCell>
        </template>
        <template v-slot:row="{ row: subjectInstance, expand, expandState }">
          <ExpandCell
            :aria-label="getExpandLabel(subjectInstance)"
            :expand-state="expandState"
            size="1"
            @click="expand()"
          />
          <Cell
            :size="isAboutOthers ? '3' : '7'"
            :column-header="$str('user_activities_title_header', 'mod_perform')"
            valign="center"
          >
            <Button
              v-if="
                currentUserHasMultipleRelationships(
                  subjectInstance.subject.participant_instances
                )
              "
              :styleclass="{ transparent: true }"
              class="tui-performUserActivityList__selectRelationshipLink"
              :text="getActivityTitle(subjectInstance.subject)"
              @click.prevent="showRelationshipSelector(subjectInstance)"
            />
            <a v-else :href="getViewActivityUrl(subjectInstance)">
              {{ getActivityTitle(subjectInstance.subject) }}
            </a>
          </Cell>
          <Cell
            size="2"
            :column-header="$str('user_activities_type_header', 'mod_perform')"
          >
            {{ subjectInstance.subject.activity.type.display_name }}
          </Cell>
          <Cell
            v-if="isAboutOthers"
            size="2"
            :column-header="
              $str('user_activities_subject_header', 'mod_perform')
            "
            valign="center"
          >
            {{ subjectInstance.subject.subject_user.fullname }}
          </Cell>
          <Cell
            v-if="hasSubjectInstanceWithJobAssignment"
            size="2"
            :column-header="
              $str('user_activities_job_assignment_header', 'mod_perform')
            "
            valign="center"
          >
            {{
              getJobAssignmentDescription(
                subjectInstance.subject.job_assignment
              )
            }}
          </Cell>
          <Cell
            v-if="isAboutOthers"
            size="2"
            :column-header="
              $str('user_activities_status_header_relationship', 'mod_perform')
            "
            valign="center"
          >
            {{
              getRelationshipText(subjectInstance.subject.participant_instances)
            }}
          </Cell>
          <Cell
            size="2"
            :column-header="
              $str('user_activities_status_header_activity', 'mod_perform')
            "
            valign="center"
          >
            {{ getStatusText(subjectInstance.subject.progress_status) }}
            <Lozenge
              v-if="subjectInstance.subject.is_overdue"
              type="alert"
              :text="$str('is_overdue', 'mod_perform')"
            />
            <Lock
              v-if="subjectInstance.subject.availability_status === 'CLOSED'"
              :alt="$str('user_activities_closed', 'mod_perform')"
              :title="$str('user_activities_closed', 'mod_perform')"
            />
          </Cell>
          <Cell
            size="2"
            :column-header="
              $str('user_activities_status_header_participation', 'mod_perform')
            "
            valign="center"
          >
            {{
              getYourProgressText(subjectInstance.subject.participant_instances)
            }}
            <Lozenge
              v-if="
                allYourInstancesAreOverdue(
                  subjectInstance.subject.participant_instances
                )
              "
              type="alert"
              :text="$str('is_overdue', 'mod_perform')"
            />
            <Lock
              v-if="
                allYourInstancesAreClosed(
                  subjectInstance.subject.participant_instances
                )
              "
              :alt="$str('user_activities_closed', 'mod_perform')"
              :title="$str('user_activities_closed', 'mod_perform')"
            />
          </Cell>
        </template>
        <template v-slot:expand-content="{ row: subjectInstance }">
          <div class="tui-performUserActivityList__expandedRow">
            <p class="tui-performUserActivityList__expandedRow-dateSummary">
              {{
                $str(
                  'user_activities_created_at',
                  'mod_perform',
                  subjectInstance.subject.created_at
                )
              }}
              <span v-if="subjectInstance.subject.due_date">
                {{
                  $str(
                    'user_activities_complete_before',
                    'mod_perform',
                    subjectInstance.subject.due_date
                  )
                }}
              </span>
              <span
                v-if="
                  isSingleSectionViewOnly(subjectInstance.subject.activity.id)
                "
              >
                {{
                  $str(
                    'user_activities_single_section_view_only_activity',
                    'mod_perform'
                  )
                }}
              </span>
            </p>

            <SectionsList
              :activity-id="subjectInstance.subject.activity.id"
              :subject-sections="subjectInstance.sections"
              :is-multi-section-active="
                subjectInstance.subject.activity.settings.multisection
              "
              :view-url="viewUrl"
              :current-user-id="currentUserId"
              :subject-user="subjectInstance.subject.subject_user"
              :anonymous-responses="
                subjectInstance.subject.activity.anonymous_responses
              "
              @single-section-view-only="flagActivitySingleSectionViewOnly"
            />

            <Button
              :text="$str('print_activity', 'mod_perform')"
              :styleclass="{ small: true }"
              @click="printActivity(subjectInstance)"
            />
          </div>
        </template>
      </Table>
      <div v-if="showLoadMore" class="tui-performUserActivityList__loadMore">
        <Button :text="$str('loadmore', 'totara_core')" @click="loadMore" />
      </div>
    </Loader>

    <ModalPresenter
      :open="isRelationshipSelectorShown"
      @request-close="hideRelationshipSelector"
    >
      <RelationshipSelector
        v-model="isRelationshipSelectorShown"
        :current-user-id="currentUserId"
        :participant-sections="selectedParticipantSections"
        :is-for-section="false"
        :subject-user="selectedSubjectUser"
        :view-url="relationshipSelectorUrl"
      />
    </ModalPresenter>
  </div>
</template>
<script>
import Button from 'tui/components/buttons/Button';
import Cell from 'tui/components/datatable/Cell';
import ExpandCell from 'tui/components/datatable/ExpandCell';
import ActivitiesFilter from 'mod_perform/components/user_activities/list/ActivitiesFilter';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Loader from 'tui/components/loading/Loader';
import Lock from 'tui/components/icons/Lock';
import Lozenge from 'tui/components/lozenge/Lozenge';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import SectionsList from 'mod_perform/components/user_activities/list/Sections';
import RelationshipSelector from 'mod_perform/components/user_activities/list/RelationshipSelector';
import Table from 'tui/components/datatable/Table';
// Query
import subjectInstancesQuery from 'mod_perform/graphql/my_subject_instances';

export default {
  components: {
    ActivitiesFilter,
    Button,
    Cell,
    ExpandCell,
    HeaderCell,
    Loader,
    Lock,
    Lozenge,
    ModalPresenter,
    RelationshipSelector,
    SectionsList,
    Table,
  },

  props: {
    /**
     * The id of the logged in user.
     */
    currentUserId: {
      required: true,
      type: Number,
    },
    about: {
      type: String,
      validator(val) {
        return ['self', 'others'].includes(val);
      },
    },
    viewUrl: {
      type: String,
      required: true,
    },
    printUrl: {
      type: String,
      required: true,
    },
    filterOptions: Object,
  },

  data() {
    return {
      subjectInstances: [],
      isRelationshipSelectorShown: false,
      selectedParticipantSections: [],
      selectedSubjectUser: {},
      singleSectionViewOnlyActivities: [],
      relationshipSelectorUrl: '',
      userFilters: {
        activityType: '',
        ownProgress: '',
        overdueOnly: false,
      },
      pagination: {
        nextCursor: '',
        total: 0,
      },
    };
  },

  computed: {
    aboutFilter() {
      return [this.about.toUpperCase()];
    },
    isAboutOthers() {
      return this.about === 'others';
    },
    /**
     * Checks if the list of subject instances has a subject with a specified job assignment.
     *
     * @return {boolean}
     */
    hasSubjectInstanceWithJobAssignment() {
      return this.subjectInstances.some(subjectInstance => {
        return (
          subjectInstance.subject &&
          subjectInstance.subject.job_assignment !== null &&
          subjectInstance.subject.job_assignment.idnumber !== null
        );
      });
    },
    hasFilter() {
      return (
        this.userFilters.activityType != '' ||
        this.userFilters.ownProgress != '' ||
        this.userFilters.overdueOnly
      );
    },
    showFilter() {
      return this.hasFilter || this.subjectInstances.length > 0;
    },
    showLoadMore() {
      return (
        this.subjectInstances.length > 0 && this.pagination.nextCursor !== ''
      );
    },
    emptyListText() {
      return this.hasFilter
        ? this.$str('user_activities_list_none_filtered', 'mod_perform')
        : this.isAboutOthers
        ? this.$str('user_activities_list_none_about_others', 'mod_perform')
        : this.$str('user_activities_list_none_about_self', 'mod_perform');
    },
    currentFilterOptions() {
      let options = {
        about: this.aboutFilter,
        activity_type: this.userFilters.activityType || null,
        participant_progress: this.userFilters.ownProgress || null,
      };

      // Not sending overdue filter option if not set.
      // If we send 'false' it will only return not overdue activities, but in this case we want ALL activities
      if (this.userFilters.overdueOnly) {
        options.overdue = true;
      }
      return options;
    },
  },

  apollo: {
    subjectInstances: {
      query: subjectInstancesQuery,
      variables() {
        return {
          filters: this.currentFilterOptions,
        };
      },
      update: data => data['mod_perform_my_subject_instances'].items,
      result({ data }) {
        this.pagination = {
          nextCursor:
            data['mod_perform_my_subject_instances'].next_cursor || '',
          total: data['mod_perform_my_subject_instances'].total || 0,
        };
      },
    },
  },

  methods: {
    /**
     * Get "view" url for a specific user activity.
     * This method should only be used in the case of single relationships.
     *
     * @param subjectInstance {{Object}}
     * @returns {string}
     * @see showRelationshipSelector
     */
    getViewActivityUrl(subjectInstance) {
      const participantSection = this.getFirstSectionToParticipate(
        subjectInstance.sections
      );
      if (participantSection) {
        return this.$url(this.viewUrl, {
          participant_section_id: participantSection.id,
        });
      }
      return '';
    },

    /**
     * Get text to describe the subject instance's job assignment.
     *
     * @param {Object|NULL} jobAssignment
     * @return {string}
     */
    getJobAssignmentDescription(jobAssignment) {
      if (!jobAssignment) {
        return this.$str('all_job_assignments', 'mod_perform');
      }
      let fullname = jobAssignment.fullname;

      if (fullname) {
        fullname = fullname.trim();
      }

      return fullname && fullname.length > 0
        ? fullname
        : this.$str(
            'unnamed_job_assignment',
            'mod_perform',
            jobAssignment.idnumber
          );
    },

    /**
     * Get the first section, if relationship id is supplied it will get the first section
     * for the user with the given relationship
     *
     * @param {Array} subjectSections
     * @return {Object|Null} returns a participant_section object
     */
    getFirstSectionToParticipate(subjectSections) {
      let foundSection = null;

      subjectSections.forEach(subjectSection => {
        let found = subjectSection.participant_sections.find(
          item => item.participant_instance.is_for_current_user
        );
        if (found && foundSection === null) {
          foundSection = found;
        }
      });

      return foundSection;
    },

    /**
     * Open the relationship selector modal.
     *
     * @param {Object} selectedSubjectInstance
     * @param {Boolean=undefined} isForPrint
     */
    showRelationshipSelector(selectedSubjectInstance, isForPrint) {
      this.selectedSubjectUser = selectedSubjectInstance.subject.subject_user;
      this.selectedParticipantSections = [];
      selectedSubjectInstance.sections.forEach(subjectSection => {
        subjectSection.participant_sections.forEach(participantSection => {
          this.selectedParticipantSections.push(participantSection);
        });
      });
      this.relationshipSelectorUrl = isForPrint ? this.printUrl : this.viewUrl;
      this.isRelationshipSelectorShown = true;
    },

    /**
     * Close the relationship selector modal.
     */
    hideRelationshipSelector() {
      this.isRelationshipSelectorShown = false;
    },

    /**
     * Get the localized status text for a particular user activity.
     *
     * @param status {String}
     * @returns {string}
     */
    getStatusText(status) {
      switch (status) {
        case 'NOT_STARTED':
          return this.$str('user_activities_status_not_started', 'mod_perform');
        case 'IN_PROGRESS':
          return this.$str('user_activities_status_in_progress', 'mod_perform');
        case 'COMPLETE':
          return this.$str('user_activities_status_complete', 'mod_perform');
        case 'PROGRESS_NOT_APPLICABLE':
          return this.$str(
            'user_activities_status_not_applicable',
            'mod_perform'
          );
        case 'NOT_SUBMITTED':
          return this.$str(
            'user_activities_status_not_submitted',
            'mod_perform'
          );
        default:
          return '';
      }
    },

    /**
     * Get the current users progress on a particular subject instance.
     *
     * @param {Object[]} participantInstances - The participant instances from the subject instance we are getting the progress text for
     * @returns {string}
     */
    getYourProgressText(participantInstances) {
      let relationships = this.filterToCurrentUser(
        participantInstances
      ).map(instance =>
        this.getParticipantStatusText(instance.progress_status)
      );

      return relationships.join(', ');
    },

    /**
     * Get the localized status text for a particular participant .
     *
     * @param status {String}
     * @returns {string}
     */
    getParticipantStatusText(status) {
      switch (status) {
        case 'NOT_STARTED':
          return this.$str(
            'participant_instance_status_not_started',
            'mod_perform'
          );
        case 'IN_PROGRESS':
          return this.$str(
            'participant_instance_status_in_progress',
            'mod_perform'
          );
        case 'COMPLETE':
          return this.$str(
            'participant_instance_status_complete',
            'mod_perform'
          );
        case 'PROGRESS_NOT_APPLICABLE':
          return this.$str(
            'participant_instance_status_progress_not_applicable',
            'mod_perform'
          );
        case 'NOT_SUBMITTED':
          return this.$str(
            'participant_instance_status_not_submitted',
            'mod_perform'
          );
        default:
          return '';
      }
    },

    /**
     * Checks if all participant instances are closed.
     *
     * @param {Array} participantInstances
     * @return {Boolean}
     */
    allYourInstancesAreClosed(participantInstances) {
      return !participantInstances.find(pi => {
        return (
          parseInt(pi.participant_id) === this.currentUserId &&
          pi.availability_status &&
          pi.availability_status !== 'CLOSED'
        );
      });
    },

    /**
     * Checks if all participant instances are overdue.
     *
     * @param {Array} participantInstances
     * @return {Boolean}
     */
    allYourInstancesAreOverdue(participantInstances) {
      return !participantInstances.find(
        pi =>
          parseInt(pi.participant_id) === this.currentUserId && !pi.is_overdue
      );
    },

    /**
     * Returns the activity title generated from the subject instance passed it.
     *
     * @param {Object} subject
     * @returns {string}
     */
    getActivityTitle(subject) {
      var title = subject.activity.name.trim();
      var suffix = subject.created_at ? subject.created_at.trim() : '';

      if (suffix) {
        return this.$str(
          'activity_title_with_subject_creation_date',
          'mod_perform',
          {
            title: title,
            date: suffix,
          }
        );
      }

      return title;
    },

    /**
     * Relationship names for the logged in user for a set of participant instances.
     *
     * @param {Object[]} participantInstances - The participant instances from the subject instance we are getting the relationship text for
     * @returns {string}
     */
    getRelationshipText(participantInstances) {
      let relationships = this.filterToCurrentUser(participantInstances).map(
        instance => instance.core_relationship.name
      );

      return relationships.join(', ');
    },

    /**
     * The label to show for the expand row button.
     *
     * @param {Object} subjectInstance
     * @returns {string}
     */
    getExpandLabel(subjectInstance) {
      const activityTitle = this.getActivityTitle(subjectInstance.subject);
      if (!this.isAboutOthers) {
        return activityTitle;
      }
      return this.$str('activity_title_for_subject', 'mod_perform', {
        activity: activityTitle,
        user: subjectInstance.subject.subject_user.fullname,
      });
    },

    /**
     * Does the logged in user have multiple relationships to the subject on an activity.
     *
     * @param {Array} participantInstances
     * @return {Boolean}
     */
    currentUserHasMultipleRelationships(participantInstances) {
      return this.filterToCurrentUser(participantInstances).length > 1;
    },

    /**
     * Filter participant instances to only ones that belong to the logged in user.
     *
     * @param {Object[]} participantInstances
     * @return {Object[]}
     */
    filterToCurrentUser(participantInstances) {
      return participantInstances.filter(pi => pi.is_for_current_user);
    },

    /**
     * Add to the list of activities that only have one section where current user has view-only access.
     *
     * @param {Number} activityId
     */
    flagActivitySingleSectionViewOnly(activityId) {
      this.singleSectionViewOnlyActivities.push(activityId);
    },

    /**
     * Find out if an activity has only one section where current user has view-only access.
     *
     * @param activityId
     * @returns {boolean}
     */
    isSingleSectionViewOnly(activityId) {
      return this.singleSectionViewOnlyActivities.includes(activityId);
    },

    /**
     * Open print-friendly page with activity.
     *
     * @param subjectInstance
     * @return {undefined}
     */
    printActivity(subjectInstance) {
      if (
        this.currentUserHasMultipleRelationships(
          subjectInstance.subject.participant_instances
        )
      ) {
        this.showRelationshipSelector(subjectInstance, true);
        return;
      }

      const participantSection = this.getFirstSectionToParticipate(
        subjectInstance.sections
      );
      const url = this.$url(this.printUrl, {
        participant_section_id: participantSection.id,
      });
      window.open(url);
    },

    /**
     * Apply the filters that have been selected.
     * This will reset pagination to the first page
     * @param {Object} filters
     */
    applyFilters(filters) {
      this.userFilters = filters;
      this.nextCursor = '';
    },

    /**
     * Load the next 'page' of results
     */
    loadMore() {
      this.$apollo.queries.subjectInstances.fetchMore({
        variables: {
          filters: this.currentFilterOptions,
          pagination: { cursor: this.pagination.nextCursor },
        },

        updateQuery: (previousResult, { fetchMoreResult }) => {
          const previousInstances =
            previousResult.mod_perform_my_subject_instances.items;
          const newInstances =
            fetchMoreResult.mod_perform_my_subject_instances.items;
          const nextCursor =
            fetchMoreResult.mod_perform_my_subject_instances.next_cursor || '';
          const total =
            fetchMoreResult.mod_perform_my_subject_instances.total || 0;
          return {
            mod_perform_my_subject_instances: {
              items: [...previousInstances, ...newInstances],
              next_cursor: nextCursor,
              total: total,
            },
          };
        },
      });
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "activity_title_for_subject",
      "activity_title_with_subject_creation_date",
      "all_job_assignments",
      "is_overdue",
      "participant_instance_status_complete",
      "participant_instance_status_in_progress",
      "participant_instance_status_not_started",
      "participant_instance_status_not_submitted",
      "participant_instance_status_progress_not_applicable",
      "print_activity",
      "unnamed_job_assignment",
      "user_activities_closed",
      "user_activities_complete_before",
      "user_activities_created_at",
      "user_activities_filter",
      "user_activities_job_assignment_header",
      "user_activities_list_none_about_others",
      "user_activities_list_none_about_self",
      "user_activities_list_none_filtered",
      "user_activities_single_section_view_only_activity",
      "user_activities_status_complete",
      "user_activities_status_header_activity",
      "user_activities_status_header_participation",
      "user_activities_status_header_relationship",
      "user_activities_status_in_progress",
      "user_activities_status_not_applicable",
      "user_activities_status_not_started",
      "user_activities_status_not_submitted",
      "user_activities_subject_header",
      "user_activities_title_header",
      "user_activities_type_header"
    ],
    "totara_core": [
      "loadmore"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performUserActivityList {
  & > * + * {
    margin-top: var(--gap-2);
  }

  &__selectRelationshipLink {
    @include tui-font-link();
  }

  &__expandedRow {
    padding: var(--gap-6) var(--gap-4);

    & > * + * {
      margin-top: var(--gap-6);
    }

    &-dateSummary {
      color: var(--color-neutral-6);
    }
  }

  &__loadMore {
    margin-top: var(--gap-3);
    text-align: center;
  }
}
</style>
