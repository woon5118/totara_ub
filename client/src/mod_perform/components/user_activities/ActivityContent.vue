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
  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
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
        <div
          v-if="!viewOnlyReportMode"
          class="tui-participantContent__user-relationship"
        >
          {{ $str('user_activities_your_relationship_to_user', 'mod_perform') }}
          <h4 class="tui-participantContent__user-relationshipValue">
            {{ relationshipToUser }}
          </h4>
        </div>

        <ResponseRelationshipSelector
          v-else
          v-model="selectedRelationshipFilter"
          :anonymous-responses="activity.anonymous_responses"
          :subject-instance-id="subjectInstanceId"
        />
      </div>

      <h2
        id="tui-participantContentHeader"
        class="tui-participantContent__header"
      >
        {{ activity.name }}
      </h2>

      <Grid :class="showSidePanel ? 'tui-participantContent__layout' : ''">
        <GridItem
          v-if="showSidePanel"
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
              v-model="navModel"
              :aria-label="false"
              @change="navChange"
            >
              <SidePanelNavGroup v-if="viewOnlyReportMode">
                <SidePanelNavButtonItem
                  v-for="siblingSection in siblingSections"
                  :id="siblingSection.id"
                  :key="siblingSection.id"
                  :text="siblingSection.display_title"
                />
              </SidePanelNavGroup>
              <SidePanelNavGroup v-else>
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
            @submit="handleSubmit"
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
                  v-if="!viewOnlyReportMode"
                  class="tui-participantContent__infoBar"
                >
                  <ResponsesAreVisibleToDescription
                    class="tui-participantContent__sectionHeadingOtherResponsesDescription"
                    :current-user-is-subject="currentUserIsSubject"
                    :visible-to-relationships="responsesAreVisibleTo"
                    :activity="activity"
                  />
                  <div
                    class="tui-participantContent__sectionHeading-otherResponseSwitch"
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
              <div
                v-if="noParticipantForRelationshipFilter"
                class="tui-participantContent__infoBar"
              >
                <em>
                  {{
                    $str('selected_relationship_not_in_section', 'mod_perform')
                  }}
                </em>
              </div>
              <template v-else>
                <div class="tui-participantContent__section-requiredContainer">
                  <span
                    class="tui-participantContent__section-responseRequired"
                    v-text="'*'"
                  />
                  {{ $str('section_element_response_required', 'mod_perform') }}
                </div>

                <div
                  v-for="sectionElement in cleanedSectionElements"
                  :key="sectionElement.id"
                  class="tui-participantContent__sectionItem"
                >
                  <h3
                    v-if="sectionElement.element.title"
                    :id="$id('title')"
                    class="tui-participantContent__sectionItem-contentHeader"
                  >
                    {{ sectionElement.element.title }}
                  </h3>

                  <RequiredOptionalIndicator
                    v-if="sectionElement.is_respondable"
                    :is-required="sectionElement.element.is_required"
                  />

                  <div class="tui-participantContent__sectionItem-content">
                    <ElementParticipantForm
                      v-if="
                        sectionElement.is_respondable && !viewOnlyReportMode
                      "
                    >
                      <template v-slot:content>
                        <component
                          :is="sectionElement.component"
                          v-bind="loadUserSectionElementProps(sectionElement)"
                        />
                      </template>
                    </ElementParticipantForm>
                    <div
                      v-else-if="!sectionElement.is_respondable"
                      class="tui-participantContent__staticElement"
                    >
                      <component
                        :is="sectionElement.component"
                        v-bind="loadUserSectionElementProps(sectionElement)"
                      />
                    </div>
                    <OtherParticipantResponses
                      v-show="showOtherResponse"
                      :view-only="viewOnlyReportMode"
                      :section-element="sectionElement"
                      :anonymous-responses="activity.anonymous_responses"
                    />
                  </div>
                </div>
              </template>
            </div>

            <ButtonGroup
              v-if="!activeSectionIsClosed && !viewOnlyReportMode"
              class="tui-participantContent__buttons"
            >
              <ButtonSubmit @click="fullSubmit(getSubmitting)" />
              <Button
                v-if="hasSaveDraft"
                :text="$str('participant_section_button_draft', 'mod_perform')"
                type="submit"
                @click="draftSubmit(getSubmitting)"
              />
              <ButtonCancel
                v-if="!isExternalParticipant"
                @click="goBackToListCancel"
              />
            </ButtonGroup>

            <div class="tui-participantContent__navigation">
              <Grid v-if="activeSectionIsClosed || viewOnlyReportMode">
                <GridItem :units="6">
                  <Button
                    v-if="previousNavSectionModel"
                    :text="$str('previous_section', 'mod_perform')"
                    @click="loadPreviousSection"
                  />
                </GridItem>
                <GridItem :units="6">
                  <div class="tui-participantContent__navigation-buttons">
                    <Button
                      v-if="nextNavSectionModel"
                      :styleclass="{ primary: 'true' }"
                      :text="$str('next_section', 'mod_perform')"
                      @click="loadNextSection"
                    />
                    <Button
                      v-if="!isExternalParticipant && !viewOnlyReportMode"
                      :text="$str('button_close', 'mod_perform')"
                      @click="goBackToListCancel"
                    />
                    <ActionLink
                      v-if="viewOnlyReportMode"
                      :text="$str('button_close', 'mod_perform')"
                      :href="backToUserReportHref"
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
import { config } from 'tui/config';
// Components
import ActionLink from 'tui/components/links/ActionLink';
import Button from 'tui/components/buttons/Button';
import ButtonCancel from 'tui/components/buttons/Cancel';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import ButtonSubmit from 'tui/components/buttons/Submit';
import Collapsible from 'tui/components/collapsible/Collapsible';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loader/Loader';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import RequiredOptionalIndicator from 'mod_perform/components/user_activities/RequiredOptionalIndicator';
import ResponsesAreVisibleToDescription from 'mod_perform/components/user_activities/participant/ResponsesAreVisibleToDescription';
import ResponseRelationshipSelector from 'mod_perform/components/user_activities/ResponseRelationshipSelector';
import SidePanel from 'tui/components/sidepanel/SidePanel';
import SidePanelNav from 'tui/components/sidepanel/SidePanelNav';
import SidePanelNavButtonItem from 'tui/components/sidepanel/SidePanelNavButtonItem';
import SidePanelNavGroup from 'tui/components/sidepanel/SidePanelNavGroup';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';
import { Uniform } from 'tui/components/uniform';
// graphQL
import SectionResponsesQuery from 'mod_perform/graphql/participant_section';
import viewOnlyReportModeSectionResponsesQuery from 'mod_perform/graphql/view_only_section_responses';
import SectionResponsesQueryExternal from 'mod_perform/graphql/participant_section_external_participant_nosession';
import UpdateSectionResponsesMutation from 'mod_perform/graphql/update_section_responses';
import UpdateSectionResponsesMutationExternalParticipant from 'mod_perform/graphql/update_section_responses_external_participant_nosession';
import { formatParams, getQueryStringParam } from 'tui/util';

const PARTICIPANT_SECTION_STATUS_COMPLETE = 'COMPLETE';

export default {
  components: {
    ActionLink,
    RequiredOptionalIndicator,
    Button,
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    Collapsible,
    ConfirmationModal,
    ElementParticipantForm,
    Grid,
    GridItem,
    Loader,
    OtherParticipantResponses,
    ParticipantUserHeader,
    ResponsesAreVisibleToDescription,
    ResponseRelationshipSelector,
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

    /**
     * The id of the logged in user.
     */
    currentUserId: {
      type: Number,
    },

    /**
     * A participant instance id, to look the section up with (used by participant mode).
     */
    participantInstanceId: {
      type: Number,
    },

    /**
     * participant section id (used by participant mode).
     */
    participantSectionId: {
      type: Number,
    },

    /**
     * subject instance id (used by view-only mode).
     */
    subjectInstanceId: {
      type: Number,
    },

    /**
     * section id (used by view-only mode).
     */
    sectionId: {
      type: Number,
    },

    /**
     * Optional token if this is an external participant (used by participant mode).
     */
    token: {
      required: false,
      type: String,
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
      progressStatus: null,
      showOtherResponse: false,
      modalOpen: false,
      formValues: {},
      participantSections: [],
      siblingSections: [],
      hasChanges: false,
      responsesAreVisibleTo: [],
      selectedRelationshipFilter: null,
      selectedParticipantSectionId: this.participantSectionId,
      selectedSectionId: this.selectedSectionId || null,
      isDraft: false,
    };
  },
  computed: {
    /**
     * Are we showing view-only (report) version,
     * this is the form not from perspective of any one participant, but as someone reviewing all other responses.
     *
     * View only mode requires the subjectInstanceId prop (sectionId is optional)
     * Participant mode requires the participantInstanceId prop (participantSectionId is optional)
     */
    viewOnlyReportMode() {
      return Boolean(this.subjectInstanceId);
    },

    /**
     * Checks draft savings is available
     *
     * @return {Boolean}
     */
    hasSaveDraft() {
      return this.progressStatus !== PARTICIPANT_SECTION_STATUS_COMPLETE;
    },

    /**
     * Get and set the section navigation model,
     * for the view-only (report) version this is a section_id,
     * for participant mode this is a participant_section_id.
     */
    navModel: {
      get() {
        if (this.viewOnlyReportMode) {
          const firstSiblingSection = this.siblingSections[0] || {};

          return this.selectedSectionId
            ? this.selectedSectionId
            : firstSiblingSection.id;
        }

        return this.selectedParticipantSectionId;
      },
      set(value) {
        if (this.viewOnlyReportMode) {
          this.selectedSectionId = value;
        } else {
          this.selectedParticipantSectionId = value;
        }
      },
    },

    /**
     * Returns true if the current user is an external participant,
     * means the token is set
     * @return {Boolean}
     */
    isExternalParticipant() {
      if (this.viewOnlyReportMode) {
        return false;
      }

      return this.token !== null && this.token.length > 0;
    },

    /**
     * Apply dynamic filtering and sorting to section elements.
     */
    cleanedSectionElements() {
      return this.sectionElements.map(sectionElement => {
        const cleanSectionElement = Object.assign({}, sectionElement);

        if (
          this.activity.anonymous_responses &&
          cleanSectionElement.other_responder_groups.length > 0
        ) {
          // Push all missing responses to the end.
          const sortedAnonGroup = this.sortMissingAnswersToEnd(
            cleanSectionElement.other_responder_groups[0]
          );

          cleanSectionElement.other_responder_groups = [sortedAnonGroup];
        } else if (this.selectedRelationshipFilter) {
          // Apply relationship filter.
          cleanSectionElement.other_responder_groups = cleanSectionElement.other_responder_groups.filter(
            group => group.relationship_name === this.selectedRelationshipFilter
          );
        }

        return cleanSectionElement;
      });
    },

    /**
     * Has a relationship filter been applied, but the selected
     * relationship is not a participant in the current section
     */
    noParticipantForRelationshipFilter() {
      if (this.selectedRelationshipFilter === null) {
        return false;
      }

      // Check that any element has "other responders".
      // The reason we need to check them all is that "other responders"
      //  are stripped off non-respondable elements in the back end.
      return !this.cleanedSectionElements.some(
        sectionElement => sectionElement.other_responder_groups.length !== 0
      );
    },

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
        !this.isExternalParticipant &&
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

    nextNavSectionModel() {
      const next = this.navModelSections[this.navModelIndex + 1];
      return next ? next.id : null;
    },

    previousNavSectionModel() {
      const previous = this.navModelSections[this.navModelIndex - 1];
      return previous ? previous.id : null;
    },

    navModelIndex() {
      const sections = this.navModelSections;

      return sections.findIndex(section => section.id == this.navModel);
    },

    navModelSections() {
      return this.viewOnlyReportMode
        ? this.siblingSections
        : this.participantSections;
    },

    /*
     * Get the participant instance we are currently answering as.
     */
    answeringAs() {
      if (
        this.viewOnlyReportMode ||
        this.answerableParticipantInstances === null
      ) {
        return null;
      }

      return this.answerableParticipantInstances.find(
        pi => Number(pi.id) === Number(this.answeringAsParticipantId)
      );
    },

    /**
     * Show navigation side panel only when there are more than one sections
     */
    showSidePanel() {
      return this.activity.settings.multisection;
    },

    /**
     * The url back to the subject user reporting page, for view-only report users.
     */
    backToUserReportHref() {
      return this.$url('/mod/perform/reporting/performance/user.php', {
        subject_user_id: this.subjectUser.id,
      });
    },
  },
  mounted() {
    // Confirm navigation away if user is currently editing.
    window.addEventListener('beforeunload', this.unloadHandler);
    window.addEventListener('popstate', this.popstateHandler);
  },
  apollo: {
    section: {
      query() {
        if (this.viewOnlyReportMode) {
          return viewOnlyReportModeSectionResponsesQuery;
        }

        return this.isExternalParticipant
          ? SectionResponsesQueryExternal
          : SectionResponsesQuery;
      },
      variables() {
        if (this.viewOnlyReportMode) {
          return {
            subject_instance_id: this.subjectInstanceId,
            section_id: this.selectedSectionId,
          };
        }

        return {
          participant_instance_id: this.answeringAsParticipantId,
          participant_section_id: this.selectedParticipantSectionId,
          token: this.token,
        };
      },
      update(data) {
        if (this.viewOnlyReportMode) {
          return data.mod_perform_view_only_section_responses.section;
        }

        return this.isExternalParticipant
          ? data.mod_perform_participant_section_external_participant.section
          : data.mod_perform_participant_section.section;
      },
      result({ data }) {
        let result;

        if (this.viewOnlyReportMode) {
          result = data.mod_perform_view_only_section_responses;
          this.siblingSections = result.siblings;
        } else {
          result = this.isExternalParticipant
            ? data.mod_perform_participant_section_external_participant
            : data.mod_perform_participant_section;

          this.selectedParticipantSectionId = result.id;
          this.answerableParticipantInstances =
            result.answerable_participant_instances;
          this.activeParticipantSection = result;
          this.participantSections =
            result.participant_instance.participant_sections;
          this.progressStatus = result.progress_status;
        }

        this.responsesAreVisibleTo = result.responses_are_visible_to;
        this.formValues = {};
        this.initialValues = {
          sectionElements: {},
        };
        this.sectionElements = result.section_element_responses.map(item => {
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
        });

        if (this.viewOnlyReportMode) {
          this.showOtherResponse = true;
          return;
        }

        result.section_element_responses
          .filter(item => item.element.is_respondable)
          .forEach(item => {
            this.initialValues.sectionElements[
              item.section_element_id
            ] = JSON.parse(item.response_data);
            this.hasOtherResponse = item.other_responder_groups.length > 0;
            item.other_responder_groups.forEach(group => {
              if (group.responses.length > 0 && item.response_data) {
                this.showOtherResponse = true;
              }
            });
          });
      },
    },
  },
  methods: {
    /**
     * Sort all missing responses to the end in a responder group.
     */
    sortMissingAnswersToEnd(responderGroup) {
      const groupClone = Object.assign({}, responderGroup);

      groupClone.responses = groupClone.responses
        .slice()
        .sort(response => (response.response_data === null ? 1 : -1));

      return groupClone;
    },

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
        props.isDraft = this.isDraft;
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
      if (this.isDraft) {
        message = 'participant_section_draft_saved';
      }
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str(message, 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Handle full and draft submit
     *
     * @param {Object} values Form values.
     */
    handleSubmit(values) {
      this.formValues = values;
      if (!this.isDraft) {
        this.modalOpen = true;
      } else {
        this.submit(this.formValues);
        this.hasUnsavedChanges = false;
      }
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
        const result = this.isExternalParticipant
          ? sectionResponsesResult.mod_perform_update_section_responses_external_participant
          : sectionResponsesResult.mod_perform_update_section_responses;
        const submittedParticipantSection = result.participant_section;
        //assign errors to individual elements
        this.errors = submittedParticipantSection.section_element_responses
          .filter(item => item.validation_errors)
          .reduce((acc, cur) => {
            cur.validation_errors.forEach(error => {
              acc[cur.section_element.id] = error.error_message;
            });
            return acc;
          }, null);

        //show validation if no errors
        if (!this.errors) {
          if (this.isDraft) {
            //stay same page and show notification
            this.showSuccessNotification();
          } else if (this.nextNavSectionModel) {
            // Redirect to next section.
            this.showSuccessNotification();
            this.loadNextSection();
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

      let inputVariables = {
        participant_section_id: this.activeParticipantSection.id,
        is_draft: this.isDraft,
        update: update,
      };

      if (this.token) {
        inputVariables.token = this.token;
      }

      const { data: resultData } = await this.$apollo.mutate({
        mutation: this.isExternalParticipant
          ? UpdateSectionResponsesMutationExternalParticipant
          : UpdateSectionResponsesMutation,
        variables: {
          input: inputVariables,
        },
        refetchAll: false,
      });
      return resultData;
    },

    /**
     * Loads the next (participant) section.
     */
    loadNextSection() {
      this.changeSection(this.nextNavSectionModel);
    },

    /**
     * Loads the previous (participant) section.
     */
    loadPreviousSection() {
      this.changeSection(this.previousNavSectionModel);
    },

    /**
     * Loads the view-only section as active section.
     */
    async reloadData() {
      await this.$apollo.queries.section.refetch();
    },

    /**
     * Redirects back to the list of user activities with a success message.
     */
    goBackToListCompletionSuccess() {
      const lang = config.locale.language;
      if (this.isExternalParticipant) {
        window.location.href = this.$url(
          '/mod/perform/activity/external.php?lang=' + lang,
          {
            success: 1,
            token: this.token,
          }
        );
        return;
      }

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
     * @param id {Number} A participant section in participant mode,
     * or a section_id for view-only mode.
     */
    navChange({ id }) {
      this.changeSection(id);
    },

    /**
     * Change the url and data to a new (participant) section.
     * @param newNavModel {Number}
     */
    changeSection(newNavModel) {
      const shouldChange = this.checkForUnsavedChanges();

      if (shouldChange) {
        this.navModel = newNavModel;
        this.updateUrl();
        this.reloadData();
      }
    },

    /**
     * Check for unsaved changes before nav change.
     */
    checkForUnsavedChanges() {
      if (this.viewOnlyReportMode || !this.hasUnsavedChanges) {
        return true;
      }

      const message = this.$str('unsaved_changes_warning', 'mod_perform');
      const confirmNav = window.confirm(message);

      if (!confirmNav) {
        this.selectedParticipantSectionId = this.activeParticipantSection.id;
        return false;
      }

      this.hasUnsavedChanges = false;
      return true;
    },

    /**
     * Push url params based of current state.
     */
    updateUrl() {
      const params = {};
      if (this.viewOnlyReportMode) {
        params.subject_instance_id = this.subjectInstanceId;

        if (this.selectedSectionId) {
          params.section_id = this.selectedSectionId;
        }
      } else {
        params.participant_section_id = this.selectedParticipantSectionId;
      }

      const formattedParams = formatParams(params);
      const url = window.location.pathname + '?' + formattedParams;

      // Note we push state by default (a new history entry) not replace it on section change.
      window.history.pushState(null, null, url);
    },
    popstateHandler() {
      const urlNavModelKey = this.viewOnlyReportMode
        ? 'section_id'
        : 'participant_section_id';

      let newNavModel = getQueryStringParam(urlNavModelKey);

      // If section_id is not in the url, assume the first section.
      if (this.viewOnlyReportMode && !newNavModel) {
        newNavModel = this.siblingSections[0].id;
      }

      if (newNavModel) {
        this.navModel = newNavModel;
        this.reloadData();
      } else {
        // Force reload if we didn't find the query string param we
        // were looking for.
        window.location.reload();
      }
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

    /**
     * Handle full submit
     *
     * @param handleSubmit
     */
    fullSubmit(handleSubmit) {
      this.isDraft = false;
      handleSubmit();
    },

    /**
     * Handle draft submit
     *
     * @param handleSubmit
     */
    draftSubmit(handleSubmit) {
      this.isDraft = true;
      handleSubmit();
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
      "participant_section_button_draft",
      "participant_section_draft_saved",
      "relation_to_subject_self",
      "section_element_response_optional",
      "section_element_response_required",
      "selected_relationship_not_in_section",
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
