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
            size="xsmall"
          />

          <ElementParticipantResponse>
            <template v-slot:content>
              <component
                :is="componentFor()"
                :data="JSON.parse(response.response_data)"
                :element="sectionElement.element"
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
  </div>
</template>
<script>
import ElementParticipantResponse from '../../element/ElementParticipantResponse';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import WarningIcon from 'tui/components/icons/common/Warning';
import { FormRow } from 'tui/components/uniform';

export default {
  components: {
    ElementParticipantResponse,
    FormRow,
    ParticipantUserHeader,
    WarningIcon,
  },

  props: {
    sectionElement: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      responderGroups: this.sectionElement.other_responder_groups,
    };
  },

  methods: {
    /**
     * Get an async component for participant response component
     * @returns {function}
     */
    componentFor() {
      return tui.asyncComponent(
        this.sectionElement.element.type.participant_response_component
      );
    },

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
      "user_activities_other_response_response",
      "user_activities_other_response_no_participants_identified"
    ]
  }
</lang-strings>
