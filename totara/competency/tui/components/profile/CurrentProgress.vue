<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module totara_competency
-->

<template>
  <div class="tui-competencyProfileCurrentProgress">
    <Grid :stack-at="900">
      <GridItem :units="10">
        <ul
          v-if="data.length"
          class="tui-competencyProfileCurrentProgress__progress"
        >
          <li v-for="(item, key) in data" :key="key">
            <AssignmentProgress :progress="item" />
          </li>
        </ul>
        <div v-else>
          {{
            $str(
              isCurrentUser
                ? 'no_current_assignments_self'
                : 'no_current_assignments_other',
              'totara_competency'
            )
          }}
        </div>
      </GridItem>
      <GridItem :units="2">
        <div class="tui-competencyProfileCurrentProgress__userDetails">
          <div
            v-if="latestAchievement"
            class="tui-competencyProfileCurrentProgress__latestAchievement"
          >
            <h4
              class="tui-competencyProfileCurrentProgress__latestAchievement-header"
            >
              {{ $str('latest_achievement', 'totara_competency') }}
            </h4>
            <div
              class="tui-competencyProfileCurrentProgress__latestAchievement-content"
            >
              {{ latestAchievement }}
            </div>
          </div>
        </div>
      </GridItem>
    </Grid>
  </div>
</template>

<script>
import AssignmentProgress from 'totara_competency/components/AssignmentProgress';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

export default {
  components: {
    AssignmentProgress,
    Grid,
    GridItem,
  },

  props: {
    data: {
      required: true,
      type: Array,
    },
    latestAchievement: {
      required: true,
      validator: prop => typeof prop === 'string' || prop === null, // String or null
    },
    isCurrentUser: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<lang-strings>
{
  "pathway_manual": [
    "rate_competencies"
  ],
  "totara_competency": [
    "assign_competencies",
    "no_current_assignments_other",
    "no_current_assignments_self",
    "self_assign_competencies",
    "latest_achievement"
  ]
}
</lang-strings>
