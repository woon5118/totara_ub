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

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @module totara_competency
-->

<template>
  <div class="tui-competencySummary">
    <div class="tui-competencySummary__header">
      <a :href="backLinkUrl">
        {{ $str('back_to', 'totara_competency', frameworkName) }}
      </a>
      <h2 class="tui-competencySummary__header-title">
        {{
          $str('competency_title', 'totara_hierarchy', {
            framework: frameworkName,
            fullname: competencyName,
          })
        }}
      </h2>
    </div>
    <General :competency-id="competencyId" />
    <LinkedCourses :competency-id="competencyId" />
    <AchievementConfiguration
      v-if="performEnabled"
      :competency-id="competencyId"
    />
  </div>
</template>

<script>
import AchievementConfiguration from 'totara_competency/components/summary/AchievementConfiguration';
import General from 'totara_competency/components/summary/CompetencySummaryGeneral';
import LinkedCourses from 'totara_competency/components/summary/LinkedCourses';

export default {
  components: {
    AchievementConfiguration,
    General,
    LinkedCourses,
  },

  props: {
    competencyId: {
      type: Number,
      required: true,
    },
    competencyName: {
      type: String,
      required: true,
    },
    frameworkId: {
      type: Number,
      required: true,
    },
    frameworkName: {
      type: String,
      required: true,
    },
    performEnabled: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    backLinkUrl() {
      return this.$url('/totara/hierarchy/index.php', {
        prefix: 'competency',
        frameworkid: this.frameworkId,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "back_to"
    ],
    "totara_hierarchy": [
      "competency_title"
    ]
  }
</lang-strings>
