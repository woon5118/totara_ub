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
  <div class="tui-otherParticipantResponses">
    <FormRow
      v-for="(group, index) in responderGroups"
      :key="index"
      :label="
        $str('user_activities_other_response_response', 'mod_perform', {
          relationship: group.relationship_name,
        })
      "
      class="tui-otherParticipantResponses__relation"
    >
      <Grid
        v-if="hasResponses(group.responses)"
        direction="vertical"
        :use-vertical-gap="false"
      >
        <GridItem>
          <Grid
            v-for="(response, responseIndex) in group.responses"
            :key="responseIndex"
            direction="vertical"
            :use-vertical-gap="false"
          >
            <GridItem>
              <div class="tui-otherParticipantResponses__response">
                <Grid direction="vertical" :use-vertical-gap="false">
                  <GridItem
                    class="tui-otherParticipantResponses__response__participant"
                  >
                    <ParticipantUserHeader
                      :user-name="
                        response.participant_instance.participant.fullname
                      "
                      :profile-picture="
                        response.participant_instance.participant
                          .profileimageurlsmall
                      "
                      size="xsmall"
                    />
                  </GridItem>
                  <GridItem>
                    <div
                      class="tui-otherParticipantResponses__response__answer"
                    >
                      <component
                        :is="componentFor()"
                        :data="JSON.parse(response.response_data)"
                        :name="sectionElement.element.name"
                        :type="sectionElement.element.type"
                      />
                    </div>
                  </GridItem>
                </Grid>
              </div>
            </GridItem>
          </Grid>
        </GridItem>
      </Grid>
      <div v-else class="tui-otherParticipantResponses__missing-participant">
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
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import ParticipantUserHeader from 'mod_perform/components/user_activities/participant/ParticipantUserHeader';
import WarningIcon from 'totara_core/components/icons/common/Warning';
import { FormRow } from 'totara_core/components/uniform';
export default {
  components: {
    Grid,
    GridItem,
    ParticipantUserHeader,
    FormRow,
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
     *  Get an async component for participant response component
     * @returns {function}
     */
    componentFor() {
      const { type } = this.sectionElement.element;
      return tui.asyncComponent(type.participant_response_component);
    },

    /**
     * Check question has other responses
     *
     * @param groupResponses
     * @returns {boolean}
     */
    hasResponses(groupResponses) {
      if (groupResponses.length > 0) {
        return true;
      }
      return false;
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
