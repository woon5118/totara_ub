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
  @package criteria_childcompetency
-->

<template>
  <CompetencyAchievementDisplay
    :achievements="achievements"
    :no-competency-msg="$str('no_competencies', 'criteria_childcompetency')"
    :user-id="userId"
    @self-assigned="$apollo.queries.achievements.refetch()"
  />
</template>

<script>
import AchievementsQuery from '../../../webapi/ajax/achievements.graphql';
import CompetencyAchievementDisplay from 'totara_criteria/components/CompetencyAchievementDisplay';

export default {
  components: { CompetencyAchievementDisplay },

  props: {
    instanceId: {
      required: true,
      type: Number,
    },
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
      achievements: {
        items: [],
      },
    };
  },

  apollo: {
    achievements: {
      query: AchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          instance_id: this.instanceId,
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ criteria_childcompetency_achievements: achievements }) {
        this.$emit('loaded');
        return achievements;
      },
    },
  },

  methods: {},
};
</script>

<lang-strings>
  {
    "criteria_childcompetency": [
      "no_competencies"
    ]
  }

</lang-strings>
