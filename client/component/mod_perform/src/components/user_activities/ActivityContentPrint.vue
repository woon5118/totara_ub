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
  <div class="tui-participantContentPrint">
    <Loader :loading="$apollo.loading">
      <ParticipantGeneralInformation
        v-if="subjectUser.card_display"
        :subject-user="subjectUser"
        :job-assignments="jobAssignments"
        :current-user-is-subject="currentUserIsSubject"
        :relationship="relationshipToUser"
      />
      <div class="tui-participantContentPrint__header">
        <h2>
          {{ activity.type.display_name }}
        </h2>

        <h1>
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
      <div>
        <RequiredOptionalIndicator is-required />
        {{ $str('section_element_response_required', 'mod_perform') }}
      </div>

      <div class="tui-participantContentPrint__section">
        <div
          v-for="sectionResponse in participantSectionResponses"
          :key="sectionResponse.id"
          class="tui-participantContentPrint__section"
        >
          <Uniform
            v-if="sectionResponse.id in initialUniformValues"
            :initial-values="initialUniformValues[sectionResponse.id]"
          >
            <div class="tui-participantContentPrint__sectionHeading">
              <h3
                v-if="activity.settings.multisection"
                class="tui-participantContentPrint__sectionHeading-title"
              >
                {{ sectionResponse.section.display_title }}
              </h3>
            </div>

            <div
              v-for="elementResponse in elementsResponsesBySection[
                sectionResponse.id
              ]"
              :key="elementResponse.id"
              class="tui-participantContentPrint__sectionItem"
            >
              <h3
                v-if="elementResponse.element.title"
                :id="$id('title')"
                class="tui-participantContentPrint__sectionItem-contentHeader"
              >
                {{ elementResponse.element.title }}
              </h3>

              <RequiredOptionalIndicator
                v-if="elementResponse.is_respondable"
                :is-required="elementResponse.element.is_required"
              />

              <div class="tui-participantContentPrint__sectionItem-content">
                <ElementParticipantForm
                  v-if="
                    elementResponse.is_respondable && sectionResponse.can_answer
                  "
                >
                  <template v-slot:content>
                    <component
                      :is="elementResponse.responseComponent"
                      v-if="sectionResponse.availability_status === 'CLOSED'"
                      class="tui-participantContentPrint__element tui-participantContentPrint__element--readOnly"
                      :element="elementResponse.element"
                      :data="JSON.parse(elementResponse.response_data)"
                    />
                    <component
                      :is="elementResponse.formComponent"
                      v-else
                      class="tui-participantContentPrint__element"
                      :element="elementResponse.element"
                      :path="['sectionElements', elementResponse.id]"
                    />
                  </template>
                </ElementParticipantForm>
                <template v-else-if="!elementResponse.is_respondable">
                  <component
                    :is="elementResponse.responseComponent"
                    v-if="sectionResponse.availability_status === 'CLOSED'"
                    :element="elementResponse.element"
                    :data="JSON.parse(elementResponse.response_data)"
                  />
                  <component
                    :is="elementResponse.formComponent"
                    v-else
                    :element="elementResponse.element"
                    :path="['sectionElements', elementResponse.id]"
                  />
                </template>
                <OtherParticipantResponses
                  :view-only="false"
                  :section-element="elementResponse"
                  :anonymous-responses="activity.anonymous_responses"
                />
              </div>
            </div>
          </Uniform>
        </div>
      </div>

      <div
        v-if="!$apollo.loading"
        class="tui-participantContentPrint__actionButtons"
      >
        <Button :text="$str('print', 'mod_perform')" @click="printActivity" />
      </div>
    </Loader>
  </div>
</template>

<script>
// Util
import { uniqueId } from 'tui/util';
import { RELATIONSHIP_SUBJECT } from 'mod_perform/constants';
// Components
import Button from 'tui/components/buttons/Button';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import Loader from 'tui/components/loading/Loader';
import OtherParticipantResponses from 'mod_perform/components/user_activities/participant/OtherParticipantResponses';
import ParticipantGeneralInformation from 'mod_perform/components/user_activities/participant/ParticipantGeneralInformation';
import RequiredOptionalIndicator from 'mod_perform/components/user_activities/RequiredOptionalIndicator';
import { Uniform } from 'tui/components/uniform';

// graphQL
import participantSectionsForPrintQuery from 'mod_perform/graphql/participant_sections_for_print';

export default {
  components: {
    RequiredOptionalIndicator,
    Button,
    ElementParticipantForm,
    Loader,
    OtherParticipantResponses,
    ParticipantGeneralInformation,
    Uniform,
  },

  props: {
    /**
     * The abstract perform activity this is an instance of.
     */
    activity: {
      type: Object,
      required: true,
    },

    /**
     * Created day of activity.
     */
    createdAt: {
      type: String,
      required: true,
    },

    /**
     * Due day of activity.
     */
    dueDate: String,

    /**
     * The user this activity is about.
     */
    subjectUser: {
      type: Object,
      required: true,
      validator(value) {
        return ['id', 'profileimageurlsmall', 'fullname'].every(
          Object.prototype.hasOwnProperty.bind(value)
        );
      },
    },

    /**
     * The participant instance id used to fetch the participant sections.
     */
    participantInstanceId: {
      type: Number,
      required: true,
    },

    /**
     * The subjects job assignments.
     */
    jobAssignments: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      participantInstance: null,
      participantSectionResponses: [],
      errors: null,
      isDraft: false,
    };
  },
  computed: {
    /**
     * Element responses keyed by participant section id.
     */
    elementsResponsesBySection() {
      const elementResponsesBySection = {};

      this.participantSectionResponses.forEach(sectionResponse => {
        elementResponsesBySection[
          sectionResponse.id
        ] = sectionResponse.section_element_responses.map(
          sectionElementResponse => {
            return {
              id: sectionElementResponse.section_element_id,
              clientId: uniqueId(),
              formComponent: tui.asyncComponent(
                sectionElementResponse.element.element_plugin
                  .participant_form_component
              ),
              responseComponent: tui.asyncComponent(
                sectionElementResponse.element.element_plugin
                  .participant_response_component
              ),
              element: {
                type: sectionElementResponse.element.element_plugin,
                title: sectionElementResponse.element.title,
                data: JSON.parse(sectionElementResponse.element.data),
                is_required: sectionElementResponse.element.is_required,
              },
              sort_order: sectionElementResponse.sort_order,
              is_respondable: sectionElementResponse.element.is_respondable,
              response_data: sectionElementResponse.response_data,
              response_data_raw: sectionElementResponse.response_data_raw,
              other_responder_groups:
                sectionElementResponse.other_responder_groups,
            };
          }
        );
      });

      return elementResponsesBySection;
    },

    /**
     * Keyed by participant section id
     */
    initialUniformValues() {
      if (this.participantSectionResponses === []) {
        return {};
      }

      const initialUniformValues = {};

      this.participantSectionResponses.forEach(sectionResponse => {
        initialUniformValues[sectionResponse.id] = { sectionElements: {} };

        this.elementsResponsesBySection[sectionResponse.id]
          .filter(elementResponse => elementResponse.is_respondable)
          .forEach(elementResponse => {
            initialUniformValues[sectionResponse.id].sectionElements[
              elementResponse.id
            ] = JSON.parse(elementResponse.response_data_raw);
          });
      });

      return initialUniformValues;
    },

    /**
     * The current users relationship to the subject of the activity.
     * @return {string|null}
     */
    relationshipToUser() {
      if (this.participantInstance === null) {
        return null;
      }

      if (this.currentUserIsSubject) {
        return this.$str('relation_to_subject_self', 'mod_perform');
      }

      return this.participantInstance.core_relationship.name;
    },

    /**
     * Is the current user the subject on this activity.
     */
    currentUserIsSubject() {
      if (this.participantInstance === null) {
        return null;
      }

      return (
        this.participantInstance.core_relationship.idnumber ===
        RELATIONSHIP_SUBJECT
      );
    },
  },
  apollo: {
    participantSectionResponses: {
      query: participantSectionsForPrintQuery,
      variables() {
        return {
          participant_instance_id: this.participantInstanceId,
        };
      },
      update: data => data['mod_perform_participant_sections'],
      result({ data }) {
        this.participantInstance = data['mod_perform_participant_instance'];
      },
    },
  },
  methods: {
    /**
     * Open the print dialog.
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
    "print",
    "relation_to_subject_self",
    "section_element_response_optional",
    "section_element_response_required",
    "user_activities_created_at",
    "user_activities_complete_before",
    "user_activities_other_response_show",
    "user_activities_your_relationship_to_user"
  ]
}
</lang-strings>

<style lang="scss">
.tui-participantContentPrint {
  @include tui-font-body();

  @media screen {
    padding: var(--gap-10);
  }

  &__user {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    padding: var(--gap-2) var(--gap-4);
    border: var(--border-width-thin) solid var(--color-border);
    border-radius: var(--border-radius-normal);

    &-relationshipValue {
      display: block;
      margin: var(--gap-1) 0 0;
    }
  }

  &__header {
    @include tui-font-heading-medium();
    margin: var(--gap-12) 0 var(--gap-4) 0;
    padding: var(--gap-2) var(--gap-4);
    text-align: center;
    border: var(--border-width-thin) solid var(--color-primary);

    &-date {
      @include tui-font-body-small();
      padding: var(--gap-6) var(--gap-4) 0;
      color: var(--color-neutral-6);
    }
  }

  &__section {
    &-requiredContainer {
      margin-top: var(--gap-2);
    }

    &-responseRequired {
      display: inline-flex;
      @include tui-font-heading-label();
      color: var(--color-primary);
    }
  }

  &__element {
    pointer-events: none;

    &--readOnly {
      padding-top: var(--gap-1);
    }
  }

  &__sectionItem {
    &-content {
      & > * {
        margin-top: var(--gap-4);
      }
    }

    &-contentHeader {
      display: inline-flex;
      @include tui-font-heading-x-small();
      margin-left: 0;
    }
  }

  &__actionButtons {
    display: flex;
    justify-content: center;
    padding-bottom: var(--gap-12);

    & > * + * {
      margin-left: var(--gap-4);
    }
  }

  @media print {
    &__actionButtons {
      display: none;
    }

    &__sectionItem {
      break-inside: avoid;
    }
  }
}
</style>
