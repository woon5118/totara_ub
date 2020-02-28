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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package pathway_manual
-->

<template>
  <div class="tui-pathwayManualAchievement">
    <template v-if="!ratingsEnabled">
      {{ $str('no_assessors_can_rate', 'pathway_manual') }}
    </template>
    <template v-else>
      <!-- Self rating collapsible group -->
      <Collapsible
        v-if="selfRate.length"
        :label="$str('self_assessment', 'pathway_manual')"
        :initial-state="true"
      >
        <AchievementDisplayRater
          :assignment-id="assignmentId"
          :rater="selfRate[0]"
          :user-id="userId"
        />
      </Collapsible>

      <!-- Rating from other collapsible group -->
      <Collapsible
        v-if="assessors.length"
        :label="$str('receive_a_rating', 'pathway_manual')"
        :initial-state="true"
      >
        <AchievementDisplayRater
          v-for="(assessor, index) in assessors"
          :key="index"
          :assignment-id="assignmentId"
          :rater="assessor"
          :user-id="userId"
        />
      </Collapsible>
    </template>
  </div>
</template>

<script>
// Components
import AchievementDisplayRater from 'pathway_manual/components/achievements/AchievementDisplayRater';
import Collapsible from 'totara_core/components/collapsible/Collapsible';

// GraphQL
import RoleRatingsQuery from 'pathway_manual/graphql/role_ratings';

export default {
  components: {
    AchievementDisplayRater,
    Collapsible,
  },

  props: {
    assignmentId: {
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
      roleRatings: [],
    };
  },

  apollo: {
    roleRatings: {
      query: RoleRatingsQuery,
      context: { batch: true },
      variables() {
        return {
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ pathway_manual_role_ratings: roleRatings }) {
        this.$emit('loaded');
        return roleRatings;
      },
    },
  },

  computed: {
    /**
     * Check if there is rating data
     *
     * @return {Boolean}
     */
    ratingsEnabled() {
      return this.roleRatings.length;
    },

    /**
     * Filter data for self rating
     *
     * @return {Array}
     */
    selfRate() {
      return this.roleRatings.filter(function(assessor) {
        return assessor.role.name === 'self';
      });
    },

    /**
     * Filter data for assessors only
     *
     * @return {Array}
     */
    assessors() {
      return this.roleRatings.filter(function(assessor) {
        return assessor.role.name !== 'self';
      });
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual" : [
      "no_assessors_can_rate",
      "receive_a_rating",
      "self_assessment"
    ]
  }
</lang-strings>
