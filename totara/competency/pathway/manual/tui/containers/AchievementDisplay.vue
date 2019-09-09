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
  @package pathway_learning_plan
-->

<template>
  <div>
    <h3>{{ $str('raters', 'pathway_manual') }}</h3>
    <div v-for="(achievement, id) in achievements" :key="id">
      {{ achievement.rater.fullname }} ({{ achievement.role }})
    </div>
  </div>
</template>

<script>
import ManualAchievementsQuery from '../../webapi/ajax/achievements.graphql';

export default {
  props: {
    userId: {
      required: true,
      type: Number,
    },
    assignmentId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      achievements: [],
    };
  },

  apollo: {
    achievements: {
      query: ManualAchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ pathway_manual_achievements: achievements }) {
        return achievements;
      },
    },
  },

  methods: {},
};
</script>

<lang-strings>
  {
    "pathway_manual" : [
      "raters"
    ]
  }
</lang-strings>
