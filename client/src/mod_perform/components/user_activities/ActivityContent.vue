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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
-->
<template>
  <Loader :loading="$apollo.loading">
    <div class="tui-participantContent">
      <ConfirmationModal
        :open="modalOpen"
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
      <div class="tui-participantContent__user">
        <ParticipantUserHeader
          :user-name="subjectUser.fullname"
          :profile-picture="subjectUser.profileimageurlsmall"
          size="small"
          class="tui-participantContent__user-info"
        />
        <div class="tui-participantContent__user-relationship">
          {{ $str('user_activities_your_relationship_to_user', 'mod_perform') }}
          <h4 class="tui-participantContent__user-relationshipValue">
            {{ relationshipToUser }}
          </h4>
        </div>
      </div>

      <h2
        id="tui-participantContentHeader"
        class="tui-participantContent__header"
      >
        {{ activity.name }}
      </h2>

      <Grid :class="showSidePanel ? 'tui-participantContent__layout' : ''">
        <GridItem
          v-show="showSidePanel"
          :units="2"
          class="tui-participantContent__sidePanel"
        >
          <SidePanel
            :initially-open="true"
            :limit-height="false"
            :show-button-control="false"
            :sticky="false"
          >
            <SidePanelNav
              v-model="selectedParticipantSectionId"
              :aria-label="false"
              @change="navChange($event)"
            >
              <SidePanelNavGroup>
                <SidePanelNavButtonItem
                  v-for="participantSection in participantSections"
                  :id="participantSection.id"
                  :key="participantSection.id"
                  :text="participantSection.section.display_title"
                />
              </SidePanelNavGroup>
            </SidePanelNav>
          </SidePanel>
        </GridItem>
        <GridItem :units="showSidePanel ? 10 : 12">
          <Uniform
            v-if="initialValues"
            :key="activeParticipantSection.id"
            v-slot="{ getSubmitting }"
            :initial-values="initialValues"
            @submit="openModal"
            @change="handleChange"
          >
            <div class="tui-participantContent__section">
              <div class="tui-participantContent__sectionHeading">
                <h3
                  v-if="activity.settings.multisection"
                  class="tui-participantContent__sectionHeading-title"
                >
                  {{ section.display_title }}
                </h3>

                <div
                  class="tui-participantContent__sectionHeadingOtherResponsesBar"
                >
                  <ResponsesAreVisibleToDescription
                    class="tui-participantContent__sectionHeadingOtherResponsesDescription"
                    :current-user-is-subject="currentUserIsSubject"
                    :visible-to-relationships="responsesAreVisibleTo"
                    :anonymous-responses="activity.anonymous_responses"
                  />
                  <div
                    class="tui-participantContent__sectionHeading-other-response-switch"
                  >
                    <ToggleSwitch
                      v-if="hasOtherResponse"
                      v-model="showOtherResponse"
                      :text="
                        $str(
                          'user_activities_other_response_show',
                          'mod_perform'
                        )
                      "
                    />
                  </div>
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
                    ({{
                      $str('section_element_response_optional', 'mod_perform')
                    }})
                  </span>
                </template>
                <div class="tui-participantContent__sectionItem-content">
                  <ElementParticipantForm>
                    <template v-slot:content>
                      <component
                        :is="sectionElement.component"
                        v-bind="loadUserSectionElementProps(sectionElement)"
                      />
                    </template>
                  </ElementParticipantForm>
                  <OtherParticipantResponses
                    v-show="showOtherResponse"
                    :section-element="sectionElement"
                    :anonymous-responses="activity.anonymous_responses"
                  />
                </div>
              </Collapsible>
            </div>

            <ButtonGroup
              v-if="!activeSectionIsClosed"
              class="tui-participantContent__buttons"
            >
              <ButtonSubmit :submitting="getSubmitting()" />
              <ButtonCancel @click="goBackToListCancel" />
            </ButtonGroup>

            <div class="tui-participantContent__navigation">
              <Grid v-if="activeSectionIsClosed">
                <GridItem :units="6">
                  <Button
                    v-if="hasPreviousSection"
                    :text="$str('previous_section', 'mod_perform')"
                    @click="loadPreviousParticipantSection"
                  />
                </GridItem>
                <GridItem :units="6">
                  <div class="tui-participantContent__navigation-buttons">
                    <Button
                      v-if="hasNextSection"
                      :styleclass="{ primary: 'true' }"
                      :text="$str('next_section', 'mod_perform')"
                      @click="loadNextParticipantSection"
                    />
                    <Button
                      :text="$str('button_close', 'mod_perform')"
                      @click="goBackToListCancel"
                    />
                  </div>
                </GridItem>
              </Grid>
            </div>
          </Uniform>
        </GridItem>
      </Grid>
    </div>
  </Loader>
</template>

<script>
// Util
import { uniqueId } from 'tui/util';
import {
  NOTIFICATION_DURATION,
  RELATIONSHIP_SUBJECT,
} from 'mod_perform/constants';
import { redirectWithPost } from 'mod_perform/redirect';
import { notify } from 'tui/notifications';
// Components
import Button from 'tui/components/buttons/Button';
import ButtonCancel from 'tui/components/buttons/Cancel';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import ButtonSubmit from 'tui/components/buttons/Submit';
import Checkbox from 'tui/components/form/Checkbox';
import Collapsible from 'tui/components/collapsible/Collapsible';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loader/Loader';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import ResponsesAreVisibleToDescription from 'mod_perform/components/user_activities/participant/ResponsesAreVisibleToDescription';
import SidePanel from 'tui/components/sidepanel/SidePanel';
import SidePanelNav from 'tui/components/sidepanel/SidePanelNav';
import SidePanelNavButtonItem from 'tui/components/sidepanel/SidePanelNavButtonItem';
import SidePanelNavGroup from 'tui/components/sidepanel/SidePanelNavGroup';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';
import { Uniform } from 'tui/components/uniform';
// graphQL
import SectionResponsesQuery from 'mod_perform/graphql/participant_section';
import UpdateSectionResponsesMutation from 'mod_perform/graphql/update_section_responses';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    Checkbox,
    Collapsible,
    ConfirmationModal,
    ElementParticipantForm,
    Grid,
    GridItem,
    Loader,
    OtherParticipantResponses,
    ParticipantUserHeader,
    ResponsesAreVisibleToDescription,
    SidePanel,
    SidePanelNav,
    SidePanelNavButtonItem,
    SidePanelNavGroup,
    ToggleSwitch,
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
      activeParticipantSection: {},
      completionSaveSuccess: false,
      errors: null,
      hasOtherResponse: false,
      hasUnsavedChanges: false,
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
      hasChanges: false,
      responsesAreVisibleTo: [],
      selectedParticipantSectionId: this.participantSectionId,
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
        this.activeParticipantSection = data.mod_perform_participant_section;
        this.participantSections =
          data.mod_perform_participant_section.participant_instance.participant_sections;
        this.responsesAreVisibleTo =
          data.mod_perform_participant_section.responses_are_visible_to;
        this.formValues = {};
        this.initialValues = {
          sectionElements: {},
        };
        this.sectionElements = data.mod_perform_participant_section.section_element_responses.map(
          item => {
            let component = this.activeSectionIsClosed
              ? item.element.element_plugin.participant_response_component
              : item.element.element_plugin.participant_form_component;
            return {
              id: item.section_element_id,
              clientId: uniqueId(),
              component: tui.asyncComponent(component),
              element: {
                type: item.element.element_plugin,
                title: item.element.title,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
                is_required: item.element.is_required,
                responseData: null,
              },
              sort_order: item.sort_order,
              is_respondable: item.element.is_respondable,
              response_data: item.response_data,
              other_responder_groups: item.other_responder_groups,
            };
          }
        );

        data.mod_perform_participant_section.section_element_responses
          .filter(item => item.element.is_respondable)
          .forEach(item => {
            this.initialValues.sectionElements[
              item.section_element_id
            ] = JSON.parse(item.response_data);
            if (item.other_responder_groups.length > 0) {
              this.hasOtherResponse = true;
            } else {
              this.hasOtherResponse = false;
            }
            item.other_responder_groups.forEach(group => {
              if (group.responses.length > 0 && item.response_data) {
                this.showOtherResponse = true;
              }
            });
          });
      },
    },
  },

  computed: {
    relationshipToUser() {
      if (this.currentUserIsSubject) {
        return this.$str('relation_to_subject_self', 'mod_perform');
      }

      return this.answeringAs ? this.answeringAs.core_relationship.name : null;
    },

    /**
     * Get the elements that can be responded to.
     */
    respondableSectionElements() {
      return this.sectionElements.filter(
        sectionElement => sectionElement.is_respondable
      );
    },

    /**
     * Is the participant answering as the subject relationship?
     */
    currentUserIsSubject() {
      return (
        this.answeringAs != null &&
        this.answeringAs.core_relationship.idnumber === RELATIONSHIP_SUBJECT
      );
    },

    /**
     * Checks if active participant section is closed.
     *
     * @return {Boolean}
     */
    activeSectionIsClosed() {
      return (
        this.activeParticipantSection &&
        this.activeParticipantSection.availability_status === 'CLOSED'
      );
    },

    /**
     * Checks if there's a next section to load for the navigation.
     *
     * @return {Boolean}
     */
    hasNextSection() {
      let nextSectionId = this.getNextParticipantSection(
        this.activeParticipantSection.id
      );

      return nextSectionId !== null;
    },

    /**
     * Checks if there's a previous section to load for the navigation.
     *
     * @return {Boolean}
     */
    hasPreviousSection() {
      let previousSectionId = this.getPreviousParticipantSection(
        this.activeParticipantSection.id
      );

      return previousSectionId !== null;
    },

    /*
     * Get the participant instance we are currently answering as.
     */
    answeringAs: {
      get() {
        if (this.answerableParticipantInstances === null) {
          return null;
        }

        return this.answerableParticipantInstances.find(
          pi => Number(pi.id) === Number(this.answeringAsParticipantId)
        );
      },
    },

    /**
     * Show navigation side panel only when there are more than one sections
     */
    showSidePanel() {
      return this.activity.settings.multisection;
    },
  },

  mounted() {
    // Confirm navigation away if user is currently editing.
    window.addEventListener('beforeunload', this.unloadHandler);
  },

  methods: {
    /**
     * Creates user section element component props
     *
     * @param {Object} sectionElement
     * @return {Object}
     */
    loadUserSectionElementProps(sectionElement) {
      let props = {
        element: sectionElement.element,
      };
      if (this.activeSectionIsClosed) {
        props.data = JSON.parse(sectionElement.response_data);
        props.class = 'tui-participantContent__readonly';
      } else {
        props.path = ['sectionElements', sectionElement.id];
        props.error = this.errors && this.errors[sectionElement.id];
      }

      return props;
    },

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
      this.hasUnsavedChanges = false;
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
            this.selectedParticipantSectionId = nextParticipantSectionId;
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
     * Extract section elements into new. update , delete and move
     * and call the GQL mutation to save section elements
     *
     * @returns {Object}
     */
    async save() {
      const update = this.respondableSectionElements.map(item => {
        return {
          section_element_id: item.id,
          response_data: JSON.stringify(item.element.responseData),
        };
      });

      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateSectionResponsesMutation,
        variables: {
          input: {
            participant_section_id: this.activeParticipantSection.id,
            update: update,
          },
        },
        refetchAll: false,
      });
      return resultData;
    },

    /**
     * Loads the next participant section.
     */
    loadNextParticipantSection() {
      let nextSection = this.getNextParticipantSection(
        this.activeParticipantSection.id
      );
      if (nextSection) {
        this.loadParticipantSection(nextSection);
      }
    },

    /**
     * Loads the previous participant section.
     */
    loadPreviousParticipantSection() {
      let previousSection = this.getPreviousParticipantSection(
        this.activeParticipantSection.id
      );
      if (previousSection) {
        this.loadParticipantSection(previousSection);
      }
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
      let indexOfCurrent = this.getIndexOfParticipantSection(
        participantSectionId
      );
      let nextParticipantSectionIndex = indexOfCurrent + 1;

      return nextParticipantSectionIndex < this.participantSections.length
        ? this.participantSections[nextParticipantSectionIndex].id
        : null;
    },

    /**
     * Get the previous participant section in reference to the provided participant section id.
     *
     * @param {Number} participantSectionId
     * @return {Number|NULL}
     */
    getPreviousParticipantSection(participantSectionId) {
      let indexOfCurrent = this.getIndexOfParticipantSection(
        participantSectionId
      );
      let previousParticipantSectionIndex = indexOfCurrent - 1;

      return previousParticipantSectionIndex >= 0
        ? this.participantSections[previousParticipantSectionIndex].id
        : null;
    },

    /**
     * Get the nexy participant section in reference to the provided participant section id.
     *
     * @param {Number} participantSectionId
     * @return {Number|NULL}
     */
    getIndexOfParticipantSection(participantSectionId) {
      return this.participantSections.findIndex(
        function(participantSection) {
          return participantSection.id === participantSectionId;
        },
        { participantSectionId }
      );
    },

    /**
     * Redirects back to the list of user activities with a success message.
     */
    goBackToListCompletionSuccess() {
      // Post requests require a real url (activity/index.php no activity/).
      const url = this.$url('/mod/perform/activity/index.php');

      redirectWithPost(url, {
        show_about_others_tab: !this.currentUserIsSubject,
        completion_save_success: true,
        closed_on_completion: this.activity.settings.close_on_completion,
      });
    },

    /**
     * Redirects back to the list of user activities on click of cancel/close.
     */
    goBackToListCancel() {
      // Post requests require a real url (activity/index.php no activity/).
      const url = this.$url('/mod/perform/activity/index.php');

      redirectWithPost(url, {
        show_about_others_tab: !this.currentUserIsSubject,
        completion_save_success: false,
      });
    },

    /**
     * check if there is unsaved changes in response form
     *
     * @param values
     */
    handleChange(values) {
      for (let i in this.initialValues.sectionElements) {
        let formValue = values.sectionElements[i];
        let initValue = JSON.stringify(this.initialValues.sectionElements[i]);
        // handle initValue is null, but formValue is empty string or array
        // convert empty formValue to null, then we can compare the difference
        let isEmptyForm =
          formValue === null || Object.values(formValue)[0].length === 0;
        if (isEmptyForm) {
          formValue = null;
        }
        formValue = JSON.stringify(formValue);
        // compare init value with form value
        if (initValue !== formValue) {
          this.hasUnsavedChanges = true;
          break;
        }
        this.hasUnsavedChanges = false;
      }
    },

    /**
     * navigate to a different participant section
     *
     * @param selectedParticipantSection
     */
    navChange(selectedParticipantSection) {
      //check unsaved changes
      if (this.hasUnsavedChanges) {
        let message = this.$str('unsaved_changes_warning', 'mod_perform');
        let answer = window.confirm(message);
        if (!answer) {
          this.selectedParticipantSectionId = this.activeParticipantSection.id;
          return;
        }
        this.hasUnsavedChanges = false;
      }

      this.updateUrlParam(selectedParticipantSection.id);
      this.loadParticipantSection(selectedParticipantSection.id);
    },

    /**
     * Update the URL param
     *
     * @param participantSectionId
     */
    updateUrlParam(participantSectionId) {
      let url = new URL(window.location.href);
      let search_params = url.searchParams;
      search_params.delete('participant_instance_id');
      search_params.set('participant_section_id', participantSectionId);
      url.search = search_params.toString();
      let new_url = url.toString();
      window.history.replaceState({}, '', new_url);
    },

    /**
     * Displays a warning message if the user tries to navigate away without saving.
     * @param {Event} e
     * @returns {String|void}
     */
    unloadHandler(e) {
      if (!this.hasUnsavedChanges) {
        return;
      }

      // For older browsers that still show custom message.
      const discardUnsavedChanges = this.$str(
        'unsaved_changes_warning',
        'mod_perform'
      );
      e.preventDefault();
      e.returnValue = discardUnsavedChanges;
      return discardUnsavedChanges;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "button_close",
      "next_section",
      "previous_section",
      "relation_to_subject_self",
      "section_element_response_optional",
      "section_element_response_required",
      "toast_error_save_response",
      "toast_success_save_close_on_completion_response",
      "toast_success_save_response",
      "unsaved_changes_warning",
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
