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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package criteria_onactivate
-->

<template>
  <div class="tui-criteriaOnActiveAchievement">
    <AchievementLayout>
      <template v-slot:left>
        <h5 class="tui-criteriaOnActiveAchievement__title">
          {{ $str('achievement_display', 'criteria_onactivate') }}
        </h5>
      </template>
    </AchievementLayout>
  </div>
</template>

<script>
import AchievementLayout from 'totara_competency/components/achievements/AchievementLayout';

export default {
  components: {
    AchievementLayout,
  },

  props: {
    assignmentId: {
      required: true,
      type: Number,
    },
    instanceId: {
      required: true,
      type: Number,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  mounted() {
    this.$emit('loaded');
  },
};
</script>

<lang-strings>
{
  "criteria_onactivate": [
    "achievement_display"
  ]
}
</lang-strings>
