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

  @author Arshad Anwer <arshad.anwer@totaralearning.com>
  @module mod_perform
-->

<template>
  <Grid
    class="tui-participantGeneralInformation"
    :use-horizontal-gap="false"
    :use-vertical-gap="false"
    :max-units="12"
    :stack-at="600"
  >
    <GridItem :units="4">
      <div class="tui-participantGeneralInformation__userDetails">
        <MiniProfileCard
          :display="subjectUser.card_display"
          :label-id="$id('user-info')"
          no-border
          read-only
        />
      </div>
    </GridItem>
    <GridItem :units="8">
      <div class="tui-participantGeneralInformation__relationship">
        <p class="tui-participantGeneralInformation__relationship-heading">
          {{
            !currentUserIsSubject
              ? $str(
                  'user_activities_your_relationship_to_user_internal',
                  'mod_perform'
                )
              : ''
          }}
          <span>{{ relationshipToUser }}</span>
        </p>

        <div class="tui-participantGeneralInformation__relationship-toSubject">
          <template
            v-if="
              currentUserIsSubject &&
                jobAssignments &&
                jobAssignments.length > 0
            "
          >
            {{ $str('user_activities_your_capacity', 'mod_perform') }}
          </template>
          <template v-if="!currentUserIsSubject">
            {{
              $str(
                'user_activities_their_capacity',
                'mod_perform',
                subjectUser.fullname
              )
            }}
          </template>
        </div>

        <JobAssignmentInformation :job-assignments="jobAssignments" />
      </div>
    </GridItem>
  </Grid>
</template>

<script>
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import JobAssignmentInformation from 'mod_perform/components/user_activities/participant/JobAssignmentInformation';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';

export default {
  components: {
    MiniProfileCard,
    JobAssignmentInformation,
    Grid,
    GridItem,
  },
  props: {
    subjectUser: {
      type: Object,
      required: true,
    },
    jobAssignments: Array,
    currentUserIsSubject: Boolean,
    relationship: String,
  },

  computed: {
    relationshipToUser() {
      if (this.currentUserIsSubject) {
        return this.$str('relation_to_subject_self_internal', 'mod_perform');
      }

      return this.relationship;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "relation_to_subject_self_internal",
      "user_activities_your_relationship_to_user_internal",
      "user_activities_their_capacity",
      "user_activities_your_capacity"
    ]
  }
</lang-strings>
