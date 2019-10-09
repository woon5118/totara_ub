<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @package totara_competency
-->

<template>
  <div>
    <div class="tui-competencySummary__header">
      <a :href="backLinkUrl" class="tui-competencySummary__header_backLink">
        {{ $str('back_to', 'totara_competency', frameworkName) }}
      </a>
      <h2 class="tui-competencySummary__header_title">
        {{ competencyTitle }}
      </h2>
    </div>
    <General :competency-id="competencyId" />
    <LinkedCourses :competency-id="competencyId" />
    <AchievementConfiguration v-if="performEnabled" :competency-id="competencyId" />
  </div>
</template>

<script>
import General from 'totara_competency/presentation/summary/General';
import LinkedCourses from 'totara_competency/presentation/summary/LinkedCourses';
import AchievementConfiguration from 'totara_competency/presentation/summary/AchievementConfiguration';

export default {
  components: {
    General,
    LinkedCourses,
    AchievementConfiguration,
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
      required: true
    }
  },

  computed: {
    backLinkUrl() {
      return this.$url('/totara/hierarchy/index.php', {
        prefix: 'competency',
        frameworkid: this.frameworkId,
      });
    },

    competencyTitle() {
      return this.$str('competencytitle', 'totara_hierarchy', {
        framework: this.frameworkName,
        fullname: this.competencyName,
      });
    },
  },
};
</script>

<style lang="scss">
.tui-competencySummary {
  &__header {
    &_backLink {
      display: inline-block;
      padding-bottom: $totara_style-spacing_1;
    }
    &_title {
      margin: 0;
      padding-bottom: $totara_style-spacing_4;
    }
  }
}
</style>

<lang-strings>
  {
    "totara_competency": [
      "back_to"
    ],
    "totara_hierarchy": [
      "competencytitle"
    ]
  }
</lang-strings>
