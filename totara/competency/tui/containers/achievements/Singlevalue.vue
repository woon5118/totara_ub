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
  @package totara_competency
-->

<template>
  <div>
    <h3>{{ $str('complete_criteria', 'totara_competency') }}</h3>
    <div v-for="(achievement, id) in achievements" :key="id">
      <h4>{{ achievement.scale_value.name }}</h4>
      <div v-for="(item, itemid) in achievement.items" :key="itemid">
        <component
          :is="item.component"
          v-bind="item.props"
          @loaded="itemLoaded"
        />
        <Divider
          v-if="!isLastItem(itemid, achievement.items)"
          :label="$str('or', 'totara_competency')"
        />
      </div>
    </div>
  </div>
</template>

<script>
import ScaleAchievementsQuery from '../../../webapi/ajax/scale_achievements.graphql';
import Divider from 'totara_competency/presentation/common/Divider';

export default {
  components: { Divider },

  props: {
    userId: {
      required: true,
      type: Number,
    },
    assignmentId: {
      required: true,
      type: Number,
    },
    type: String,
  },

  data: function() {
    return {
      achievements: [],
      achievementComponents: [],
      itemsLoaded: 0,
    };
  },

  computed: {
    numberOfItems() {
      let count = 0;

      if (this.achievements.length > 0) {
        this.achievements.forEach(achievement => {
          count += achievement.items.length;
        });
      }

      return count;
    },
  },

  watch: {
    itemsLoaded: function(newLoading) {
      // If all items are loaded
      if (newLoading === this.numberOfItems) {
        this.$emit('loaded');
      }
    },
  },

  apollo: {
    achievements: {
      query: ScaleAchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          assignment_id: this.assignmentId,
        };
      },
      update({ totara_competency_scale_achievements: achievements }) {
        var newAchievements = [];

        achievements.forEach(achievement => {
          var newAchievement = {
            scale_value: achievement.scale_value,
            items: [],
          };
          achievement.items.forEach(item => {
            let compPath = `pathway_${item.pathway_type}/containers/AchievementDisplay`;

            newAchievement.items.push({
              component: tui.asyncComponent(compPath),
              props: {
                userId: this.userId,
                assignmentId: this.assignmentId,
                instanceId: parseInt(item.instance_id),
              },
            });
          });

          newAchievements.push(newAchievement);
        });

        return newAchievements;
      },
    },
  },

  methods: {
    isLastItem(id, items) {
      return id === items.length - 1;
    },

    itemLoaded() {
      this.itemsLoaded += 1;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency" : [
      "complete_criteria",
      "or"
    ]
  }
</lang-strings>
