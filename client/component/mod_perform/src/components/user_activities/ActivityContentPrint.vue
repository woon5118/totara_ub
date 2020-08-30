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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module mod_perform
-->
<template>
  <Loader :loading="$apollo.loading">
    <div class="tui-participantContent">
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
      </div>

      <div class="tui-participantContentPrint__header">
        <h2 class="tui-participantContentPrint__header-type">
          {{ activity.type.display_name }}
        </h2>

        <h1 class="tui-participantContentPrint__header-name">
          {{ activity.name }}
        </h1>

        <p class="tui-participantContentPrint__header-date">
          {{ $str('user_activities_created_at', 'mod_perform', createdAt) }}
          <span v-if="dueDate">
            {{
              $str('user_activities_complete_before', 'mod_perform', dueDate)
            }}
          </span>
        </p>
      </div>
      <div class="tui-participantContent__section-required-container">
        <span
          class="tui-participantContent__section-response-required"
          v-text="'*'"
        />
        {{ $str('section_element_response_required', 'mod_perform') }}
      </div>

      <Uniform v-if="initialValues" :initial-values="initialValues">
        <div class="tui-participantContent__section">
          <div
            v-for="participantSection in participantSections"
            :key="participantSection.id"
            class="tui-participantContent__section"
          >
            <div class="tui-participantContent__sectionHeading">
              <h3
                v-if="activity.settings.multisection"
                class="tui-participantContent__sectionHeading-title"
              >
                {{ participantSection.section.display_title }}
              </h3>
            </div>

            <div
              v-for="sectionElement in sectionElements[participantSection.id]"
              :key="sectionElement.id"
              class="tui-participantContent__sectionItem"
            >
              <div v-if="sectionElements[participantSection.id]">
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
                    v-if="sectionElement.is_respondable && participantCanAnswer &&
                            !viewOnlyReportMode"
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
                    :view-only="viewOnlyReportMode"
                    :section-element="sectionElement"
                    :anonymous-responses="activity.anonymous_responses"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </Uniform>
      <Button
        class="tui-performUserActivityList__action-button"
        :text="$str('print_activity', 'mod_perform')"
        @click="printActivity()"
      />
    </div>
  </Loader>
</template>

<script>
// Util
import { uniqueId } from 'tui/util';
import { RELATIONSHIP_SUBJECT } from 'mod_perform/constants';
// Components
import Button from 'tui/components/buttons/Button';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import Loader from 'tui/components/loader/Loader';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import RequiredOptionalIndicator from 'mod_perform/components/user_activities/RequiredOptionalIndicator';
import { Uniform } from 'tui/components/uniform';

// graphQL
import SectionResponsesQuery from 'mod_perform/graphql/participant_section';
import viewOnlyReportModeSectionResponsesQuery from 'mod_perform/graphql/view_only_section_responses';
import SectionResponsesQueryExternal from 'mod_perform/graphql/participant_section_external_participant_nosession';

export default {
  components: {
    RequiredOptionalIndicator,
    Button,
    ElementParticipantForm,
    Loader,
    OtherParticipantResponses,
    ParticipantUserHeader,
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
     * Created day of activity.
     */
    createdAt: String,

    /**
     * Due day of activity.
     */
    dueDate: String,

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
    currentUserId: Number,

    /**
     * A participant instance id, to look the section up with (used by participant mode).
     */
    participantInstanceId: Number,

    /**
     * participant section id (used by participant mode).
     */
    participantSectionId: Number,

    /**
     * subject instance id (used by view-only mode).
     */
    subjectInstanceId: Number,

    /**
     * section id (used by view-only mode).
     */
    sectionId: Number,

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
      errors: null,
      hasOtherResponse: false,
      initialValues: null,
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      progressStatus: null,
      showOtherResponse: false,
      formValues: {},
      participantSections: [],
      responsesAreVisibleTo: [],
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

    participantCanAnswer() {
      return this.activeParticipantSection.can_answer;
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

    relationshipToUser() {
      if (this.currentUserIsSubject) {
        return this.$str('relation_to_subject_self', 'mod_perform');
      }

      return this.answeringAs ? this.answeringAs.core_relationship.name : null;
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

    navModelIndex() {
      const sections = this.navModelSections;

      return sections.findIndex(section => section.id == this.navModel);
    },

    nextNavSectionModel() {
      const next = this.navModelSections[this.navModelIndex + 1];
      return next ? next.id : null;
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
        this.sectionElements[result.id] = result.section_element_responses.map(
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

        if (this.viewOnlyReportMode|| !this.participantCanAnswer) {
          this.showOtherResponse = true;
          return;
        }

        result.section_element_responses
          .filter(item => item.element.is_respondable)
          .forEach(item => {
            this.initialValues.sectionElements[result.id] = {};
            this.initialValues.sectionElements[result.id][
              item.section_element_id
              ] = JSON.parse(item.response_data);
            this.hasOtherResponse = item.other_responder_groups.length > 0;
            item.other_responder_groups.forEach(group => {
              if (group.responses.length > 0 && item.response_data) {
                this.showOtherResponse = true;
              }
            });
          });

        setTimeout(() => {
          if (this.navModelSections[this.navModelIndex + 1]) {
            this.changeSection(this.nextNavSectionModel);
          }
        }, 0);
      },
    },
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
        props.isDraft = this.isDraft;
        props.path = ['sectionElements', sectionElement.id];
        props.error = this.errors && this.errors[sectionElement.id];
      }

      return props;
    },

    /**
     * Loads the view-only section as active section.
     */
    async reloadData() {
      await this.$apollo.queries.section.refetch();
    },

    /**
     * Change the url and data to a new (participant) section.
     * @param newNavModel {Number}
     */
    changeSection(newNavModel) {
      this.navModel = newNavModel;
      this.reloadData();
    },

    /**
     * Print activity.
     */
    printActivity() {
      window.print();
    },
  },
};
</script>
<lang-strings>
{
"mod_perform": [
"print_activity",
"relation_to_subject_self",
"section_element_response_optional",
"section_element_response_required",
"user_activities_created_at",
"user_activities_complete_before",
"user_activities_other_response_show",
"user_activities_your_relationship_to_user"
],
"moodle": [
"submit"
]
}
</lang-strings>

<style lang="scss">
.tui-participantContentPrint {
  @include tui-font-body();

  &__header {
    @include tui-font-heading-medium();
    margin: var(--gap-12) 0 var(--gap-4) 0;
    padding: var(--gap-2) var(--gap-4);
    text-align: center;
    border: var(--border-width-thin) solid var(--color-prompt-success);

    &-date {
      @include tui-font-body-small();
      padding: var(--gap-6) var(--gap-4) 0;
      color: var(--color-neutral-6);
    }
  }
}
</style>

