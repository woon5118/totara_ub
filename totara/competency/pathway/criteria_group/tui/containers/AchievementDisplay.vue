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
  @package pathway_criteria_group
-->

<template>
  <div class="tui-pathwayCriteriaGroup-achievement__group">
    <div v-for="(component, id) in achievements" :key="id">
      <div class="tui-pathwayCriteriaGroup-achievement__group__criteria">
        <component
          :is="component.component"
          v-bind="component.props"
          @loaded="itemLoaded"
        />
      </div>
      <Divider
        v-if="!isLastItem(id, achievements)"
        :label="$str('and', 'totara_competency')"
      />
    </div>
  </div>
</template>

<script>
import CriteriaGroupAchievementsQuery from '../../webapi/ajax/achievements.graphql';
import Divider from 'totara_competency/presentation/common/Divider';

export default {
  components: { Divider },
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
      achievements: [],
      itemsLoaded: 0,
    };
  },

  computed: {
    numberOfItems() {
      return this.achievements.length;
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
      query: CriteriaGroupAchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          instance_id: this.instanceId,
        };
      },
      update({ pathway_criteria_group_achievements: achievements }) {
        let newAchievementComponents = [];
        achievements.forEach(achievement => {
          let compPath = `criteria_${achievement.type}/containers/AchievementDisplay`;

          newAchievementComponents.push({
            component: tui.asyncComponent(compPath),
            props: {
              userId: this.userId,
              assignmentId: this.assignmentId,
              instanceId: parseInt(achievement.instance_id),
            },
          });
        });

        return newAchievementComponents;
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

<style lang="scss">
.tui-pathwayCriteriaGroup-achievement {
  &__group {
    margin: 1em;
    padding: 1em;
    border: 1px dashed grey;
    border-radius: 6px;

    &__criteria {
      margin: 1em;
      padding: 1em;
      border: 1px solid black;
      border-radius: 6px;
    }
  }
}
</style>

<lang-strings>
  {
    "totara_competency" : [
      "and"
    ]
  }
</lang-strings>
