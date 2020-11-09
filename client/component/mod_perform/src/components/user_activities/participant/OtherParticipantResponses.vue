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
  <div class="tui-otherParticipantResponses">
    <template v-if="!anonymousResponses">
      <FormRow
        v-for="(group, index) in responderGroups"
        :key="index"
        :label="
          $str('user_activities_other_response_response', 'mod_perform', {
            relationship: group.relationship_name,
          })
        "
      >
        <div
          v-if="hasResponses(group.responses)"
          class="tui-otherParticipantResponses__response"
        >
          <div
            v-for="(response, responseIndex) in group.responses"
            :key="responseIndex"
            class="tui-otherParticipantResponses__response-participant"
          >
            <ParticipantUserHeader
              :user-name="response.participant_instance.participant.fullname"
              :profile-picture="
                response.participant_instance.participant.profileimageurlsmall
              "
              size="xxsmall"
            />

            <ElementParticipantResponse>
              <template v-slot:content>
                <component
                  :is="sectionElement.responseDisplayComponent"
                  :element="sectionElement.element"
                  :data="response.response_data"
                  :response-lines="response.response_data_formatted_lines"
                />
              </template>
            </ElementParticipantResponse>
          </div>
        </div>
        <div v-else class="tui-otherParticipantResponses__noParticipant">
          {{
            $str(
              'user_activities_other_response_no_participants_identified',
              'mod_perform'
            )
          }}
        </div>
      </FormRow>
    </template>
    <template v-else>
      <FormRow
        v-for="(group, index) in responderGroups"
        :key="index"
        :label="anonymousGroupLabel"
      >
        <div class="tui-otherParticipantResponses__anonymousResponse">
          <div
            v-for="(response, responseIndex) in group.responses"
            :key="responseIndex"
            class="tui-otherParticipantResponses__anonymousResponse-participant"
          >
            <ElementParticipantResponse>
              <template v-slot:content>
                <component
                  :is="sectionElement.responseDisplayComponent"
                  :element="sectionElement.element"
                  :data="response.response_data"
                  :response-lines="response.response_data_formatted_lines"
                />
              </template>
            </ElementParticipantResponse>
          </div>
        </div>
      </FormRow>
    </template>
  </div>
</template>
<script>
import ElementParticipantResponse from '../../element/ElementParticipantResponse';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import WarningIcon from 'tui/components/icons/Warning';
import { FormRow } from 'tui/components/uniform';

export default {
  components: {
    ElementParticipantResponse,
    FormRow,
    ParticipantUserHeader,
    WarningIcon,
  },

  props: {
    viewOnly: Boolean,
    sectionElement: {
      type: Object,
      required: true,
    },
    anonymousResponses: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    /**
     * @return {Object} The section element "other responder groups", with the response data parsed.
     */
    responderGroups() {
      return this.sectionElement.other_responder_groups.map(group => {
        const responses = group.responses.map(response => {
          return Object.assign({}, response, {
            response_data: JSON.parse(response.response_data),
          });
        });

        return Object.assign({}, group, { responses });
      });
    },
    anonymousGroupLabel() {
      if (this.viewOnly) {
        return this.$str('responses', 'mod_perform');
      }

      return this.$str('response_other', 'mod_perform');
    },
  },
  methods: {
    /**
     * Check question has other responses
     *
     * @param groupResponses
     * @returns {boolean}
     */
    hasResponses(groupResponses) {
      return groupResponses.length > 0;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "responses",
      "response_other",
      "user_activities_other_response_response",
      "user_activities_other_response_no_participants_identified"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-otherParticipantResponses {
  & > * + * {
    margin-top: var(--gap-8);
  }

  &__response {
    & > * + * {
      margin-top: var(--gap-6);
    }

    &-participant {
      & > * + * {
        margin-top: var(--gap-2);
      }
    }
  }

  &__noParticipant {
    margin-top: var(--gap-1);
    @include tui-font-hint();
  }
}
</style>
