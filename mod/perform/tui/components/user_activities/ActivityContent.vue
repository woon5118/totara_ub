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
    <Uniform
      v-if="initialValues"
      v-slot="{ getSubmitting }"
      :initial-values="initialValues"
      @submit="submit"
    >
      <div class="tui-participantContent__user">
        <div class="tui-participantContent__user-info">
          <ParticipantUserHeader
            :user-name="subjectInstance.subject_user.fullname"
            :profile-picture="subjectInstance.subject_user.profileimageurlsmall"
            size="small"
          />
        </div>
        <div class="tui-participantContent__user-relationship">
          {{ $str('user_activities_your_relationship_to_user', 'mod_perform') }}
          <h4 class="tui-participantContent__user-relationshipValue">
            {{ subjectInstance.relationship_to_subject }}
          </h4>
        </div>
      </div>

      <h2 class="tui-participantContent__header">
        {{ subjectInstance.activity.name }}
      </h2>

      <div class="tui-participantContent__section">
        <div class="tui-participantContent__sectionHeading">
          <h3 class="tui-participantContent__sectionHeading-title">
            {{ section.title }}
          </h3>

          <Checkbox
            v-show="hasOtherResponse"
            v-model="showOtherResponse"
            class="tui-participantContent__sectionHeading-switch"
          >
            {{ $str('user_activities_other_response_show', 'mod_perform') }}
          </Checkbox>
        </div>

        <Collapsible
          v-for="sectionElement in sectionElements"
          :key="sectionElement.id"
          :label="sectionElement.element.name"
          :initial-state="true"
          class="tui-participantContent__sectionItem"
        >
          <div class="tui-participantContent__sectionItem-content">
            <component
              :is="sectionElement.component"
              :path="['sectionElements', sectionElement.id]"
              :data="sectionElement.element.data"
              :name="sectionElement.element.name"
              :type="sectionElement.element.type"
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
  </div>
</template>

<script>
// Util
import { uniqueId } from 'totara_core/util';
import { notify } from 'totara_core/notifications';
// Components
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonSubmit from 'totara_core/components/buttons/Submit';
import Checkbox from 'totara_core/components/form/Checkbox';
import Collapsible from 'totara_core/components/collapsible/Collapsible';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
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
    OtherParticipantResponses,
    ParticipantUserHeader,
    Uniform,
  },

  props: {
    subjectInstance: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      errors: null,
      hasOtherResponse: false,
      initialValues: null,
      isSaving: false,
      participantSectionId: null,
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      showOtherResponse: false,
      completionSaveSuccess: false,
    };
  },

  apollo: {
    section: {
      query: SectionResponsesQuery,
      variables() {
        return {
          subject_instance_id: this.subjectInstance.id,
        };
      },
      update: data => data.mod_perform_participant_section.section,
      result({ data }) {
        this.participantSectionId = data.mod_perform_participant_section.id;
        this.initialValues = {};
        this.initialValues.sectionElements = {};
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
                name: item.element.title,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
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
    /**
     * Determine if we should return the the about others tab on cancel or completion.
     */
    shouldReturnToAboutOthersTab() {
      if (!this.subjectInstance) {
        return false;
      }

      return !this.subjectInstance.is_self;
    },
  },

  methods: {
    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: 10000,
        message: this.$str('toast_error_save_response', 'mod_perform'),
        type: 'error',
      });
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
        const result = values.sectionElements[sectionElement.id];
        sectionElement.element.data = result;
      });

      this.isSaving = true;
      try {
        const sectionResponsesResult = await this.save();
        const element_responses =
          sectionResponsesResult.mod_perform_update_section_responses
            .participant_section.section_element_responses;
        //assign errors to individual elements
        this.errors = element_responses
          .filter(item => item.validation_errors)
          .reduce((acc, cur) => {
            cur.validation_errors.forEach(error => {
              acc[cur.section_element.id] = error.error_message;
            });
            return acc;
          }, null);

        //show validation if no errors
        if (!this.errors) {
          this.goBackToListCompletionSuccess();
        }
        this.isSaving = false;
      } catch (e) {
        this.showErrorNotification();
        this.isSaving = false;
      } finally {
        this.isSaving = false;
      }
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
          response_data: JSON.stringify(item.element.data),
        };
      });

      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateSectionResponsesMutation,
        variables: {
          input: {
            participant_section_id: this.participantSectionId,
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
        show_about_others_tab: this.shouldReturnToAboutOthersTab,
        completion_save_success: true,
      });
    },

    goBackToListCancel() {
      // Post requests require a real url (activity/index.php no activity/).
      const url = this.$url('/mod/perform/activity/index.php');

      this.redirectWithPost(url, {
        show_about_others_tab: this.shouldReturnToAboutOthersTab,
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
      "user_activities_other_response_show",
      "user_activities_your_relationship_to_user",
      "toast_success_save_response",
      "toast_error_save_response"
    ]
  }
</lang-strings>
