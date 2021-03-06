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
  @module mod_perform
-->
<template>
  <div class="tui-performUserActivityListSections">
    <div
      v-for="subjectSection in subjectSectionsSubset"
      :key="subjectSection.section.id"
      class="tui-performUserActivityListSection"
    >
      <h3
        v-if="isMultiSectionActive"
        class="tui-performUserActivityListSection__header"
      >
        <template v-if="subjectSection.canParticipate">
          <Button
            v-if="currentUserHasMultipleRelationships(subjectSection)"
            class="tui-performUserActivityListSection__selectRelationshipLink"
            :styleclass="{ transparent: true }"
            :text="subjectSection.section.display_title"
            @click="showRelationshipSelector(subjectSection)"
          />
          <a
            v-else
            :href="
              $url(viewUrl, {
                participant_section_id: getFirstSectionIdToParticipate(
                  subjectSection
                ),
              })
            "
          >
            {{ subjectSection.section.display_title }}
          </a>
          <span v-if="!subjectSection.canCurrentUserAnswer">
            {{ $str('user_activities_section_view_only', 'mod_perform') }}
          </span>
        </template>
        <template v-else>
          {{ subjectSection.section.display_title }}
        </template>
      </h3>

      <Table
        v-if="
          !anonymousResponses ||
            subjectSection.participationToDisplay.length > 0
        "
        :data="subjectSection.participationToDisplay"
        class="tui-performUserActivityListSection__data"
        :border-bottom-hidden="true"
        :border-separator-hidden="true"
        :border-top-hidden="!isMultiSectionActive"
      >
        <template v-slot:row="{ row }">
          <Cell
            size="4"
            :column-header="
              $str('user_activities_status_header_relationship', 'mod_perform')
            "
            :heavy="row.isForCurrentUser"
            valign="center"
          >
            {{ row.relationship }}
          </Cell>
          <Cell
            size="4"
            :column-header="
              $str('user_activities_subject_header', 'mod_perform')
            "
            :heavy="row.isForCurrentUser"
            valign="center"
          >
            <ParticipantUserHeader
              :user-name="
                row.isForCurrentUser
                  ? $str('user_activities_you', 'mod_perform')
                  : row.participant.fullname
              "
              :profile-picture="row.participant.profileimageurlsmall"
              size="xxsmall"
            />
          </Cell>
          <Cell
            size="4"
            :column-header="
              $str(
                'user_activities_status_header_section_progress',
                'mod_perform'
              )
            "
            :heavy="row.isForCurrentUser"
            valign="center"
          >
            {{ getStatusText(row.progressStatus) }}
            <Lock
              v-if="row.availabilityStatus === 'CLOSED'"
              :alt="$str('user_activities_closed', 'mod_perform')"
              :title="$str('user_activities_closed', 'mod_perform')"
            />
            <Lozenge
              v-if="row.isOverdue"
              type="alert"
              :text="$str('is_overdue', 'mod_perform')"
            />
          </Cell>
        </template>
      </Table>

      <template v-if="anonymousResponses">
        <hr />

        <div class="tui-performUserActivityListSection__summary">
          <p>
            {{
              $str(
                'user_activities_total_respondents',
                'mod_perform',
                subjectSection.summary.totalRespondents
              )
            }}
          </p>
          <p>
            {{
              $str(
                'user_activities_total_completed',
                'mod_perform',
                subjectSection.summary.totalCompleted
              )
            }}
          </p>
        </div>
      </template>
    </div>

    <ModalPresenter
      :open="isRelationshipSelectorShown"
      @request-close="hideRelationshipSelector"
    >
      <RelationshipSelector
        v-model="isRelationshipSelectorShown"
        :current-user-id="currentUserId"
        :participant-sections="selectedParticipantSections"
        :is-for-section="true"
        :subject-user="subjectUser"
        :view-url="viewUrl"
      />
    </ModalPresenter>
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import Cell from 'tui/components/datatable/Cell';
import Lock from 'tui/components/icons/Lock';
import Lozenge from 'tui/components/lozenge/Lozenge';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import RelationshipSelector from 'mod_perform/components/user_activities/list/RelationshipSelector';
import Table from 'tui/components/datatable/Table';

export default {
  components: {
    Button,
    Cell,
    Lock,
    Lozenge,
    ModalPresenter,
    ParticipantUserHeader,
    RelationshipSelector,
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
    isMultiSectionActive: {
      required: true,
      type: Boolean,
    },
    subjectSections: {
      required: true,
      type: Array,
    },
    viewUrl: {
      required: true,
      type: String,
    },
    subjectUser: {
      required: true,
      type: Object,
    },
    anonymousResponses: {
      required: true,
      type: Boolean,
    },
    activityId: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      isRelationshipSelectorShown: false,
      selectedParticipantSections: [],
    };
  },

  computed: {
    /**
     * Reduce the data set to the required values for this template
     *
     */
    subjectSectionsSubset() {
      return this.subjectSections.map(item => {
        const participation = item.participant_sections.map(
          participantSection => {
            let relationship,
              relationship_id,
              participant = {};

            const isForCurrentUser =
              participantSection.participant_instance.is_for_current_user;

            // These fields are not included if answers are anonymous.
            if (isForCurrentUser || !this.anonymousResponses) {
              relationship =
                participantSection.participant_instance.core_relationship.name;
              relationship_id =
                participantSection.participant_instance.core_relationship.id;
              participant = participantSection.participant_instance.participant;
            }

            return {
              id: participantSection.id,
              isForCurrentUser,
              progressStatus: participantSection.progress_status,
              availabilityStatus: participantSection.availability_status,
              isOverdue: participantSection.is_overdue,
              canAnswer: participantSection.can_answer,
              participant,
              relationship,
              relationship_id,
            };
          }
        );

        const filteredParticipation = participation.filter(
          participantSection => {
            if (!this.anonymousResponses) {
              return true;
            }

            return participantSection.isForCurrentUser;
          }
        );

        const participationToDisplay = this.filterToCanAnswer(
          filteredParticipation
        );

        const canCurrentUserAnswer =
          this.filterToCurrentUser(participationToDisplay).length > 0;

        return {
          canParticipate: item.can_participate,
          canCurrentUserAnswer,
          participation: filteredParticipation,
          participationToDisplay,
          summary: this.getParticipantSummary(participation),
          participant_sections: item.participant_sections,
          section: item.section,
        };
      });
    },
  },

  mounted() {
    this.checkSingleSectionViewOnly();
  },

  methods: {
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
     * Get the first section id, if relationship id is supplied it
     * will get the first section for the user
     *
     * @param {Object} subjectSection
     * @return {Int}
     */
    getFirstSectionIdToParticipate(subjectSection) {
      let item = subjectSection.participation.find(
        item => item.isForCurrentUser
      );
      if (!item) {
        throw 'Section for user not found.';
      }
      return item.id;
    },

    /**
     * Does the logged in user have multiple relationships to the subject on an activity.
     *
     * @param {Object} subjectSection
     * @return {Boolean}
     */
    currentUserHasMultipleRelationships(subjectSection) {
      return this.filterToCurrentUser(subjectSection.participation).length > 1;
    },

    /**
     * Open the relationship selector modal.
     *
     * @param {Object} subjectSection
     */
    showRelationshipSelector(subjectSection) {
      this.selectedParticipantSections = [];
      subjectSection.participant_sections.forEach(participantSection => {
        this.selectedParticipantSections.push(participantSection);
      });
      this.isRelationshipSelectorShown = true;
    },

    /**
     * Close the relationship selector modal.
     */
    hideRelationshipSelector() {
      this.isRelationshipSelectorShown = false;
    },

    /**
     * Filter section participation to only ones that belong to the logged in user.
     *
     * @param {Object[]} participation
     * @return {Object[]}
     */
    filterToCurrentUser(participation) {
      return participation.filter(ps => ps.isForCurrentUser);
    },

    /**
     * Filter section participation to only ones that can answer.
     *
     * @param {Object[]} participation
     * @return {Object[]}
     */
    filterToCanAnswer(participation) {
      return participation.filter(ps => ps.canAnswer);
    },

    getParticipantSummary(participation) {
      const respondents = this.filterToCanAnswer(participation);
      const totalRespondents = respondents.length;
      const totalCompleted = respondents.filter(
        item => item.progressStatus === 'COMPLETE'
      ).length;

      return {
        totalRespondents,
        totalCompleted,
      };
    },

    /**
     * Let parent component know when we find out that we only have one section and it is view-only for the
     * current user.
     */
    checkSingleSectionViewOnly() {
      if (
        this.subjectSectionsSubset.length === 1 &&
        !this.subjectSectionsSubset[0].canCurrentUserAnswer
      ) {
        this.$emit('single-section-view-only', this.activityId);
      }
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "is_overdue",
      "user_activities_closed",
      "user_activities_section_view_only",
      "user_activities_status_complete",
      "user_activities_status_header_relationship",
      "user_activities_status_header_section_progress",
      "user_activities_status_in_progress",
      "user_activities_status_not_started",
      "user_activities_status_not_submitted",
      "user_activities_subject_header",
      "user_activities_total_completed",
      "user_activities_total_respondents",
      "user_activities_you"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performUserActivityListSections {
  & > * + * {
    margin-top: var(--gap-12);
  }
}

.tui-performUserActivityListSection {
  max-width: 800px;

  & > * + * {
    margin-top: var(--gap-2);
  }

  &__header {
    margin: 0;
    @include tui-font-body();
  }

  &__selectRelationshipLink {
    text-align: left;
    @include tui-font-link();
  }
}
</style>
