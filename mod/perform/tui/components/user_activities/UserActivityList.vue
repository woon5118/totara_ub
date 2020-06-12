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
  @package mod_perform
-->
<template>
  <Loader :loading="$apollo.loading">
    <Table
      v-if="!$apollo.loading"
      :data="subjectInstances"
      class="tui-performUserActivityList"
    >
      <template v-slot:header-row>
        <HeaderCell :size="isAboutOthers ? '4' : '8'">
          {{ $str('user_activities_title_header', 'mod_perform') }}
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
          {{
            $str('user_activities_status_header_participation', 'mod_perform')
          }}
        </HeaderCell>
        <HeaderCell size="2">
          {{ $str('user_activities_status_header_activity', 'mod_perform') }}
        </HeaderCell>
      </template>
      <template v-slot:row="{ row: subjectInstance }">
        <Cell
          :size="isAboutOthers ? '4' : '8'"
          :column-header="$str('user_activities_title_header', 'mod_perform')"
        >
          <Button
            v-if="
              currentUserHasMultipleRelationships(
                subjectInstance.participant_instances
              )
            "
            :styleclass="{ transparent: true }"
            class="tui-performUserActivityList__select-relationship-link"
            :text="subjectInstance.activity.name"
            @click.prevent="showRelationshipSelector(subjectInstance)"
          />
          <a v-else :href="getViewActivityUrl(subjectInstance)">
            {{ subjectInstance.activity.name }}
          </a>
        </Cell>
        <Cell
          v-if="isAboutOthers"
          size="2"
          :column-header="$str('user_activities_subject_header', 'mod_perform')"
        >
          {{ subjectInstance.subject_user.fullname }}
        </Cell>
        <Cell
          v-if="isAboutOthers"
          size="2"
          :column-header="
            $str('user_activities_status_header_relationship', 'mod_perform')
          "
        >
          {{ getRelationshipText(subjectInstance.participant_instances) }}
        </Cell>
        <Cell
          size="2"
          :column-header="
            $str('user_activities_status_header_participation', 'mod_perform')
          "
        >
          {{ getYourProgressText(subjectInstance.participant_instances) }}
        </Cell>
        <Cell
          size="2"
          :column-header="
            $str('user_activities_status_header_activity', 'mod_perform')
          "
        >
          {{ getStatusText(subjectInstance.progress_status) }}
        </Cell>
      </template>
    </Table>

    <ModalPresenter
      :open="relationshipSelector"
      @request-close="hideRelationshipSelector"
    >
      <Modal :aria-labelledby="$id('select-relationship-title')">
        <ModalContent
          v-if="selectedSubjectInstance"
          :title="
            $str('select_relationship_to_respond_as_title', 'mod_perform')
          "
          :title-id="$id('select-relationship-title')"
          :close-button="false"
        >
          <p :id="$id('select-relationship-explanation')">
            {{
              $str(
                'select_relationship_to_respond_as_explanation',
                'mod_perform',
                selectedSubjectInstance.subject_user.fullname
              )
            }}
          </p>

          <RadioGroup
            v-model="relationshipToRespondAs"
            required
            :aria-labelledby="$id('select-relationship-explanation')"
          >
            <Radio
              v-for="participantInstance in respondAsOptions"
              :key="participantInstance.core_relationship.id"
              :value="participantInstance.core_relationship.id"
              name="relationshipToRespondAs"
            >
              {{
                $str(
                  'select_relationship_to_respond_as_option',
                  'mod_perform',
                  {
                    relationship_name:
                      participantInstance.core_relationship.name,
                    progress_status: getStatusText(
                      participantInstance.progress_status
                    ),
                  }
                )
              }}
            </Radio>
          </RadioGroup>

          <template v-slot:buttons>
            <Button
              :styleclass="{ primary: true }"
              :text="$str('continue', 'moodle')"
              :disabled="!relationshipToRespondAs || relationshipConfirmed"
              @click="confirmRelationshipSelection"
            />
            <CancelButton
              :disabled="relationshipConfirmed"
              @click="hideRelationshipSelector"
            />
          </template>
        </ModalContent>
      </Modal>
    </ModalPresenter>
  </Loader>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import CancelButton from 'totara_core/components/buttons/Cancel';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Loader from 'totara_core/components/loader/Loader';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Table from 'totara_core/components/datatable/Table';

// Queries
import subjectInstancesQuery from 'mod_perform/graphql/subject_instances';

export default {
  components: {
    Button,
    CancelButton,
    Cell,
    HeaderCell,
    Loader,
    Modal,
    ModalContent,
    ModalPresenter,
    Radio,
    RadioGroup,
    Table,
  },

  props: {
    about: {
      type: String,
      validator(val) {
        return ['self', 'others'].includes(val);
      },
    },
    // The id of the logged in user.
    currentUserId: {
      required: true,
      type: Number,
    },
    viewUrl: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      subjectInstances: [],
      relationshipSelector: false,
      selectedSubjectInstance: null,
      relationshipToRespondAs: null,
      relationshipConfirmed: false,
    };
  },

  computed: {
    aboutFilter() {
      return [this.about.toUpperCase()];
    },
    isAboutOthers() {
      return this.about === 'others';
    },
    respondAsOptions() {
      if (this.selectedSubjectInstance === null) {
        return [];
      }

      return this.filterToCurrentUser(
        this.selectedSubjectInstance.participant_instances
      );
    },
    selectedParticipantInstance() {
      return this.selectedSubjectInstance.participant_instances.filter(pi => {
        return pi.core_relationship.id === this.relationshipToRespondAs;
      })[0];
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
      update: data => data['mod_perform_subject_instances'],
    },
  },

  methods: {
    confirmRelationshipSelection() {
      this.relationshipConfirmed = true;
      window.location = this.$url(this.viewUrl, {
        participant_instance_id: this.selectedParticipantInstance.id,
      });
    },

    /**
     * Does the logged in user have multiple relationships to the subject on an activity.
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
     * Get "view" url for a specific user activity.
     * This method should only be used in the case of single relationships.
     *
     * @param subjectInstance {{Object}}
     * @returns {string}
     * @see showRelationshipSelector
     */
    getViewActivityUrl(subjectInstance) {
      const participant_instance = this.filterToCurrentUser(
        subjectInstance.participant_instances
      )[0];

      return this.$url(this.viewUrl, {
        participant_instance_id: participant_instance.id,
      });
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
     * Close the relationship selector modal.
     */
    hideRelationshipSelector() {
      this.relationshipSelector = false;
      this.selectedSubjectInstance = null;
      this.relationshipToRespondAs = null;
      this.relationshipConfirmed = false;
    },

    /**
     * Open the relationship selector modal.
     */
    showRelationshipSelector(selectedSubjectInstance) {
      this.relationshipSelector = true;
      this.selectedSubjectInstance = selectedSubjectInstance;
      this.relationshipToRespondAs = this.respondAsOptions[0].core_relationship.id;
      this.relationshipConfirmed = false;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "select_relationship_to_respond_as_explanation",
      "select_relationship_to_respond_as_option",
      "select_relationship_to_respond_as_title",
      "user_activities_status_complete",
      "user_activities_status_header_activity",
      "user_activities_status_header_participation",
      "user_activities_status_header_relationship",
      "user_activities_status_in_progress",
      "user_activities_status_not_started",
      "user_activities_subject_header",
      "user_activities_title_header"
    ],
    "moodle": [
      "continue"
    ]
  }
</lang-strings>
