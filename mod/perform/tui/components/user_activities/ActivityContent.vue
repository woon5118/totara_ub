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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-participantContent">
    <Loader :loading="$apollo.loading">
      <ConfirmationModal
        :open="modalOpen"
        :close-button="false"
        :confirm-button-text="$str('submit', 'moodle')"
        :title="
          $str('user_activities_submit_confirmation_title', 'mod_perform')
        "
        @confirm="confirmModal"
        @cancel="cancelModal"
      >
        <p>
          {{
            $str('user_activities_submit_confirmation_message', 'mod_perform')
          }}
        </p>
        <p v-if="activity.settings.close_on_completion">
          {{
            $str(
              'user_activities_close_on_completion_submit_confirmation_message',
              'mod_perform'
            )
          }}
        </p>
      </ConfirmationModal>
      <Uniform
        v-if="initialValues"
        :key="activeParticipantSectionId"
        v-slot="{ getSubmitting }"
        :initial-values="initialValues"
        @submit="openModal"
      >
        <div class="tui-participantContent__user">
          <ParticipantUserHeader
            :user-name="subjectUser.fullname"
            :profile-picture="subjectUser.profileimageurlsmall"
            size="small"
            class="tui-participantContent__user-info"
          />
          <div class="tui-participantContent__user-relationship">
            {{
              $str('user_activities_your_relationship_to_user', 'mod_perform')
            }}
            <h4 class="tui-participantContent__user-relationshipValue">
              {{ relationshipToUser }}
            </h4>
          </div>
        </div>

        <h2 class="tui-participantContent__header">
          {{ activity.name }}
        </h2>

        <div class="tui-participantContent__section">
          <div class="tui-participantContent__sectionHeading">
            <h3 class="tui-participantContent__sectionHeading-title">
              {{ section.display_title }}
            </h3>

            <div
              class="tui-participantContent__sectionHeading-other-response-switch"
            >
              <ToggleButton
                v-show="hasOtherResponse"
                v-model="showOtherResponse"
                :text="
                  $str('user_activities_other_response_show', 'mod_perform')
                "
              />
            </div>
          </div>
          <div class="tui-participantContent__section-required-container">
            <span
              class="tui-participantContent__section-response-required"
              v-text="'*'"
            />
            {{ $str('section_element_response_required', 'mod_perform') }}
          </div>

          <Collapsible
            v-for="sectionElement in sectionElements"
            :key="sectionElement.id"
            :label="sectionElement.element.title"
            :initial-state="true"
            class="tui-participantContent__sectionItem"
          >
            <template v-slot:label-extra>
              <span
                v-if="sectionElement.element.is_required"
                class="tui-participantContent__section-response-required"
              >
                *
              </span>
              <span
                v-if="!sectionElement.element.is_required"
                class="tui-participantContent__response-optional"
              >
                ({{ $str('section_element_response_optional', 'mod_perform') }})
              </span>
            </template>
            <div class="tui-participantContent__sectionItem-content">
              <component
                :is="sectionElement.component"
                :path="['sectionElements', sectionElement.id]"
                :data="sectionElement.element.data"
                :title="sectionElement.element.title"
                :type="sectionElement.element.type"
                :is-required="sectionElement.element.is_required"
                :error="errors && errors[sectionElement.clientId]"
              />
              <OtherParticipantResponses
                v-show="showOtherResponse"
                :section-element="sectionElement"
              />
            </div>
          </Collapsible>
        </div>

        <ButtonGroup class="tui-participantContent__buttons">
          <ButtonSubmit :submitting="getSubmitting()" />
          <ButtonCancel @click="goBackToListCancel" />
        </ButtonGroup>
      </Uniform>
    </Loader>
  </div>
</template>

<script>
// Util
import { uniqueId } from 'totara_core/util';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import { notify } from 'totara_core/notifications';
// Components
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonSubmit from 'totara_core/components/buttons/Submit';
import Checkbox from 'totara_core/components/form/Checkbox';
import Collapsible from 'totara_core/components/collapsible/Collapsible';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import Loader from 'totara_core/components/loader/Loader';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import ToggleButton from 'totara_core/components/buttons/ToggleButton';
import { Uniform } from 'totara_core/components/uniform';
// graphQL
import SectionResponsesQuery from 'mod_perform/graphql/participant_section';
import UpdateSectionResponsesMutation from 'mod_perform/graphql/update_section_responses';

export default {
  components: {
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    Checkbox,
    Collapsible,
    ConfirmationModal,
    Loader,
    OtherParticipantResponses,
    ParticipantUserHeader,
    ToggleButton,
    Uniform,
  },

  props: {
    /**
     * The abstract perform activity this is an instance of.
     */
    activity: {
      required: true,
      type: Object,
    },

    /**
     * The id of the logged in user.
     */
    currentUserId: {
      required: true,
      type: Number,
    },

    /**
     * A participant instance id, to look the section up with.
     * This will change when we introduce multiple sections.
     */
    participantInstanceId: {
      type: Number,
    },

    /**
     * participant section id
     */
    participantSectionId: {
      type: Number,
    },

    /**
     * The user this activity is about.
     */
    subjectUser: {
      required: true,
      type: Object,
      validator(value) {
        return ['id', 'profileimageurlsmall', 'fullname'].every(
          Object.prototype.hasOwnProperty.bind(value)
        );
      },
    },
  },

  data() {
    return {
      answerableParticipantInstances: null,
      answeringAsParticipantId: this.participantInstanceId,
      activeParticipantSectionId: this.participantSectionId,
      completionSaveSuccess: false,
      errors: null,
      hasOtherResponse: false,
      initialValues: null,
      isSaving: false,
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      showOtherResponse: false,
      modalOpen: false,
      formValues: {},
      participantSections: [],
    };
  },

  apollo: {
    section: {
      query: SectionResponsesQuery,
      variables() {
        return {
          participant_instance_id: this.answeringAsParticipantId,
          participant_section_id: this.participantSectionId,
        };
      },
      update: data => data.mod_perform_participant_section.section,
      result({ data }) {
        this.answerableParticipantInstances =
          data.mod_perform_participant_section.answerable_participant_instances;
        this.activeParticipantSectionId =
          data.mod_perform_participant_section.id;
        this.participantSections =
          data.mod_perform_participant_section.participant_instance.participant_sections;
        this.initialValues = {
          sectionElements: {},
        };
        this.sectionElements = data.mod_perform_participant_section.section_element_responses.map(
          item => {
            return {
              id: item.section_element_id,
              clientId: uniqueId(),
              component: tui.asyncComponent(
                item.element.element_plugin.participant_form_component
              ),
              element: {
                type: item.element.element_plugin,
                title: item.element.title,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
                is_required: item.element.is_required,
                responseData: null,
              },
              sort_order: item.sort_order,
              response_data: item.response_data,
              other_responder_groups: item.other_responder_groups,
            };
          }
        );

        data.mod_perform_participant_section.section_element_responses.forEach(
          item => {
            this.initialValues.sectionElements[
              item.section_element_id
            ] = JSON.parse(item.response_data);
            if (item.other_responder_groups.length > 0) {
              this.hasOtherResponse = true;
            }
            item.other_responder_groups.forEach(group => {
              if (group.responses.length > 0 && item.response_data) {
                this.showOtherResponse = true;
              }
            });
          }
        );
      },
    },
  },

  computed: {
    relationshipToUser() {
      if (this.currentUserIsSubject) {
        return this.$str('relation_to_subject_self', 'mod_perform');
      }

      return this.answeringAs.core_relationship.name;
    },

    /**
     * Is the logged in user the subject of this activity.
     */
    currentUserIsSubject() {
      return Number(this.currentUserId) === Number(this.subjectUser.id);
    },

    /*
     * Get the participant instance we are currently answering as.
     */
    answeringAs: {
      get() {
        if (this.answerableParticipantInstances === null) {
          return {};
        }

        return this.answerableParticipantInstances.find(
          pi => Number(pi.id) === Number(this.answeringAsParticipantId)
        );
      },
    },
  },

  methods: {
    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_save_response', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Show a generic success toast.
     */
    showSuccessNotification() {
      let message = this.activity.settings.close_on_completion
        ? 'toast_success_save_close_on_completion_response'
        : 'toast_success_save_response';
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str(message, 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Shows Confirmation Modal
     * @param {Object} values Form values.
     */
    openModal(values) {
      this.formValues = values;
      this.modalOpen = true;
    },

    /**
     * Confirms confirmation modal.
     */
    confirmModal() {
      this.submit(this.formValues);
      this.modalOpen = false;
    },

    /**
     * Close confirmation modal.
     */
    cancelModal() {
      this.modalOpen = false;
    },

    /**
     * Save user responses and show notifications
     *
     * @param {Object} values
     */
    async submit(values) {
      if (this.errors) {
        this.errors = null;
      }

      // assign values from submission to the section elements
      this.sectionElements.forEach(sectionElement => {
        sectionElement.element.responseData =
          values.sectionElements[sectionElement.id];
      });

      this.isSaving = true;
      try {
        const sectionResponsesResult = await this.save();
        const submittedParticipantSection =
          sectionResponsesResult.mod_perform_update_section_responses
            .participant_section;
        //assign errors to individual elements
        this.errors = submittedParticipantSection.section_element_responses
          .filter(item => item.validation_errors)
          .reduce((acc, cur) => {
            cur.validation_errors.forEach(error => {
              acc[cur.section_element.id] = error.error_message;
            });
            return acc;
          }, null);
        let nextParticipantSectionId = this.getNextParticipantSection(
          submittedParticipantSection.id
        );

        //show validation if no errors
        if (!this.errors) {
          if (nextParticipantSectionId) {
            // Redirect to next section.
            this.showSuccessNotification();
            await this.loadParticipantSection(nextParticipantSectionId);
          } else {
            // Go back to activity list
            this.goBackToListCompletionSuccess();
          }
        }
      } catch (e) {
        this.showErrorNotification();
      }
      this.isSaving = false;
    },

    /**
     * Loads the participant section as active participant section.
     *
     * @param {Number} participantSectionId
     * @return {NULL}
     */
    async loadParticipantSection(participantSectionId) {
      await this.$apollo.queries.section.refetch({
        participant_section_id: participantSectionId,
      });
    },

    /**
     * Get the next available participant section to fill.
     *
     * @param {Number} participantSectionId Completed participant section id
     * @return {Number|NULL} next participant section id
     */
    getNextParticipantSection(participantSectionId) {
      let indexOfCurrent = this.participantSections.findIndex(
        function(participantSection) {
          return participantSection.id === participantSectionId;
        },
        { participantSectionId }
      );
      let nextParticipantSectionIndex = indexOfCurrent + 1;

      return nextParticipantSectionIndex < this.participantSections.length
        ? this.participantSections[nextParticipantSectionIndex].id
        : null;
    },

    /**
     * Extract section elements into new. update , delete and move
     * and call the GQL mutation to save section elements
     *
     * @returns {Object}
     */
    async save() {
      const update = this.sectionElements.map(item => {
        return {
          section_element_id: item.id,
          response_data: JSON.stringify(item.element.responseData),
        };
      });

      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateSectionResponsesMutation,
        variables: {
          input: {
            participant_section_id: this.activeParticipantSectionId,
            update: update,
          },
        },
        refetchAll: false,
      });
      return resultData;
    },

    goBackToListCompletionSuccess() {
      // Post requests require a real url (activity/index.php no activity/).
      const url = this.$url('/mod/perform/activity/index.php');

      this.redirectWithPost(url, {
        show_about_others_tab: !this.currentUserIsSubject,
        completion_save_success: true,
        closed_on_completion: this.activity.settings.close_on_completion,
      });
    },

    goBackToListCancel() {
      // Post requests require a real url (activity/index.php no activity/).
      const url = this.$url('/mod/perform/activity/index.php');

      this.redirectWithPost(url, {
        show_about_others_tab: !this.currentUserIsSubject,
        completion_save_success: false,
      });
    },

    /**
     * There is no real way to do a post request redirect in js
     * This just creates a hidden form and submits it.
     *
     * @param {String} url
     * @param {Object} params
     */
    redirectWithPost(url, params) {
      const hiddenForm = document.createElement('form');
      hiddenForm.style.display = 'hidden';
      hiddenForm.action = url;
      hiddenForm.method = 'post';

      // Note this only supports boolean params.
      Object.entries(params).forEach(entry => {
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.name = entry[0];
        input.checked = Boolean(entry[1]);
        hiddenForm.appendChild(input);
      });

      document.body.appendChild(hiddenForm);

      hiddenForm.submit();
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "relation_to_subject_self",
      "section_element_response_required",
      "section_element_response_optional",
      "toast_error_save_response",
      "toast_success_save_close_on_completion_response",
      "toast_success_save_response",
      "user_activities_close_on_completion_submit_confirmation_message",
      "user_activities_other_response_show",
      "user_activities_submit_confirmation_message",
      "user_activities_submit_confirmation_title",
      "user_activities_your_relationship_to_user"
    ],
    "moodle": [
       "submit"
    ]
  }
</lang-strings>
