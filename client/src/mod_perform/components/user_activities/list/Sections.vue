<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
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
        <Button
          v-if="
            subjectSection.canParticipate &&
              currentUserHasMultipleRelationships(subjectSection)
          "
          :styleclass="{ transparent: true }"
          :text="subjectSection.section.display_title"
          @click.prevent="showRelationshipSelector(subjectSection)"
        />
        <a
          v-else-if="subjectSection.canParticipate"
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
        <template v-else>
          {{ subjectSection.section.display_title }}
        </template>
      </h3>

      <Table
        v-if="!anonymousResponses || subjectSection.participation.length > 0"
        :data="subjectSection.participation"
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
            <Avatar
              :src="row.participant.profileimageurlsmall"
              :alt="row.participant.fullname"
              size="xsmall"
              class="tui-bulkManualRatingRateUsersList__avatar"
            />
            <template v-if="row.isForCurrentUser">
              {{ $str('user_activities_you', 'mod_perform') }}
            </template>

            <template v-else>
              {{ row.participant.fullname }}
            </template>
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
import Avatar from 'totara_core/components/avatar/Avatar';
import Button from 'totara_core/components/buttons/Button';
import Cell from 'totara_core/components/datatable/Cell';
import Lock from 'totara_core/components/icons/common/Lock';
import Lozenge from 'totara_core/components/lozenge/Lozenge';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import RelationshipSelector from 'mod_perform/components/user_activities/list/RelationshipSelector';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Avatar,
    Button,
    Cell,
    Lock,
    Lozenge,
    ModalPresenter,
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

        return {
          canParticipate: item.can_participate,
          participation: filteredParticipation,
          summary: this.getParticipantSummary(participation),
          participant_sections: item.participant_sections,
          section: item.section,
        };
      });
    },
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
     * Filter participant instances to only ones that belong to the logged in user.
     *
     * @param {Object[]} participantSections
     * @return {Object[]}
     */
    filterToCurrentUser(participantSections) {
      return participantSections.filter(ps => ps.isForCurrentUser);
    },

    getParticipantSummary(participation) {
      const totalRespondents = participation.length;
      const totalCompleted = participation.filter(
        item => item.progressStatus === 'COMPLETE'
      ).length;

      return {
        totalRespondents,
        totalCompleted,
      };
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "is_overdue",
      "user_activities_closed",
      "user_activities_status_complete",
      "user_activities_status_header_relationship",
      "user_activities_status_header_section_progress",
      "user_activities_status_in_progress",
      "user_activities_status_not_started",
      "user_activities_subject_header",
      "user_activities_total_completed",
      "user_activities_total_respondents",
      "user_activities_you"
    ]
  }
</lang-strings>
