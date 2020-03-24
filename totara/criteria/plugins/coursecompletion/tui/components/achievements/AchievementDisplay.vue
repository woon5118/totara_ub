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
  @package criteria_coursecompletion
-->

<template>
  <CourseAchievementDisplay :achievements="achievements" />
</template>

<script>
// Components
import CourseAchievementDisplay from 'totara_criteria/components/achievements/CourseAchievementDisplay';
// GraphQL
import AchievementsQuery from 'criteria_coursecompletion/graphql/achievements';

export default {
  components: { CourseAchievementDisplay },
  inheritAttrs: false,
  props: {
    instanceId: {
      required: true,
      type: Number,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      achievements: {
        items: [],
      },
    };
  },

  apollo: {
    /**
     * Fetch a criteria set for course completion
     *
     * @return {Object}
     */
    achievements: {
      query: AchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          instance_id: this.instanceId,
          user_id: this.userId,
        };
      },
      update({ criteria_coursecompletion_achievements: achievements }) {
        this.$emit('loaded');
        return achievements;
      },
    },
  },
};
</script>
