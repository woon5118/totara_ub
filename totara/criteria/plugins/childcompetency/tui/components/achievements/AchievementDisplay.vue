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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package criteria_childcompetency
-->

<template>
  <CompetencyAchievementDisplay
    type="childCompetency"
    :achievements="achievements"
    :user-id="userId"
    @self-assigned="$apollo.queries.achievements.refetch()"
  />
</template>

<script>
// Components
import CompetencyAchievementDisplay from 'totara_criteria/components/achievements/CompetencyAchievementDisplay';
// GraphQL
import AchievementsQuery from 'criteria_childcompetency/graphql/achievements';

export default {
  components: { CompetencyAchievementDisplay },

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

  data() {
    return {
      achievements: {
        items: [],
      },
    };
  },

  apollo: {
    /**
     * Fetch a criteria set for child competency completion
     *
     * @return {Object}
     */
    achievements: {
      query: AchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          assignment_id: this.assignmentId,
          instance_id: this.instanceId,
          user_id: this.userId,
        };
      },
      update({ criteria_childcompetency_achievements: achievements }) {
        this.$emit('loaded');
        return achievements;
      },
    },
  },
};
</script>
