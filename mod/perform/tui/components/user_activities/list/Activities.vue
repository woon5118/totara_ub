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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @package mod_perform
-->
<template>
  <Loader :loading="$apollo.loading">
    <Table
      v-if="!$apollo.loading"
      :data="subjectInstances"
      :expandable-rows="true"
      class="tui-performUserActivityList"
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
        <ExpandCell :expand-state="expandState" size="1" @click="expand()" />
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
            class="tui-performUserActivityList__select-relationship-link"
            :text="subjectInstance.subject.activity.name"
            @click.prevent="showRelationshipSelector(subjectInstance)"
          />
          <a v-else :href="getViewActivityUrl(subjectInstance)">
            {{ subjectInstance.subject.activity.name }}
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
          :column-header="$str('user_activities_subject_header', 'mod_perform')"
          valign="center"
        >
          {{ subjectInstance.subject.subject_user.fullname }}
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
        <p class="tui-performUserActivityDateSummary">
          {{
            $str(
              'user_activities_created_at',
              'mod_perform',
              subjectInstance.subject.created_at
            )
          }}
        </p>
        <SectionsList
          :subject-sections="subjectInstance.sections"
          :is-multi-section-active="
            subjectInstance.subject.activity.settings.multisection
          "
          :view-url="viewUrl"
          :current-user-id="currentUserId"
          :subject-user="subjectInstance.subject.subject_user"
        />
      </template>
    </Table>

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
        :view-url="viewUrl"
      />
    </ModalPresenter>
  </Loader>
</template>
<script>
import Button from 'totara_core/components/buttons/Button';
import Cell from 'totara_core/components/datatable/Cell';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Loader from 'totara_core/components/loader/Loader';
import Lock from 'totara_core/components/icons/common/Lock';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import SectionsList from 'mod_perform/components/user_activities/list/Sections';
import RelationshipSelector from 'mod_perform/components/user_activities/list/RelationshipSelector';
import Table from 'totara_core/components/datatable/Table';
// Query
import subjectInstancesQuery from 'mod_perform/graphql/my_subject_instances';

export default {
  components: {
    Button,
    Cell,
    ExpandCell,
    HeaderCell,
    Loader,
    Lock,
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
  },

  data() {
    return {
      subjectInstances: [],
      isRelationshipSelectorShown: false,
      selectedParticipantSections: [],
      selectedSubjectUser: {},
    };
  },

  computed: {
    aboutFilter() {
      return [this.about.toUpperCase()];
    },
    isAboutOthers() {
      return this.about === 'others';
    },
  },

  apollo: {
    subjectInstances: {
      query: subjectInstancesQuery,
      fetchPolicy: 'network-only', // Always refetch data on tab change
      variables() {
        return {
          filters: {
            about: this.aboutFilter,
          },
        };
      },
      update: data => data['mod_perform_my_subject_instances'],
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
     */
    showRelationshipSelector(selectedSubjectInstance) {
      this.selectedSubjectUser = selectedSubjectInstance.subject.subject_user;
      this.selectedParticipantSections = [];
      selectedSubjectInstance.sections.forEach(subjectSection => {
        subjectSection.participant_sections.forEach(participantSection => {
          this.selectedParticipantSections.push(participantSection);
        });
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
     * Get the current users progress on a particular subject instance.
     *
     * We have to take into account that there can be several participant instances per row (e.g. when the user is both
     * appraiser and manager for the subject). So we get an overall status depending on the combination of individual
     * statuses.
     *
     * @param {Object[]} participantInstances - The participant instances from the subject instance we are getting the progress text for
     * @returns {string}
     */
    getYourProgressText(participantInstances) {
      let allComplete = true;
      let allNotStarted = true;
      this.filterToCurrentUser(participantInstances).forEach(function(
        participant_instance
      ) {
        switch (participant_instance.progress_status) {
          case 'NOT_STARTED':
            allComplete = false;
            break;
          case 'IN_PROGRESS':
            allComplete = false;
            allNotStarted = false;
            break;
          case 'COMPLETE':
            allNotStarted = false;
        }
      });
      let calcStatus = allNotStarted
        ? 'NOT_STARTED'
        : allComplete
        ? 'COMPLETE'
        : 'IN_PROGRESS';
      return this.getStatusText(calcStatus);
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
      return participantInstances.filter(
        pi => Number(this.currentUserId) === Number(pi.participant_id)
      );
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "user_activities_closed",
      "user_activities_created_at",
      "user_activities_status_complete",
      "user_activities_status_header_activity",
      "user_activities_status_header_participation",
      "user_activities_status_header_relationship",
      "user_activities_status_in_progress",
      "user_activities_status_not_started",
      "user_activities_subject_header",
      "user_activities_title_header",
      "user_activities_type_header"
    ]
  }
</lang-strings>
