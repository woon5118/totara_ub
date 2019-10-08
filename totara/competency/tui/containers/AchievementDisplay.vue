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
    <div
      v-for="(component, id) in achievements"
      :key="id"
      class="tui-totaraCompetency-achievementPathDisplay__group"
    >
      <component
        :is="component.component"
        v-bind="component.props"
        @loaded="itemLoaded"
      />
    </div>
  </div>
</template>

<script>
import AchievementPathsQuery from '../../webapi/ajax/achievement_paths.graphql';

export default {
  components: {},
  props: {
    userId: {
      required: true,
      type: Number,
    },
    assignment: {
      required: true,
      type: Object,
    },
  },

  data: function() {
    return {
      achievements: [],
      itemsLoaded: null,
    };
  },

  computed: {
    assignmentId() {
      return parseInt(this.assignment.id);
    },

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
      query: AchievementPathsQuery,
      context: { batch: true },
      variables() {
        return {
          assignment_id: this.assignmentId,
        };
      },
      update({ totara_competency_achievement_paths: paths }) {
        let newAchievementComponents = [];
        paths.forEach(path => {
          let componentName = this.capitalizeFirstLetter(path.class);
          let compPath = `totara_competency/containers/achievements/${componentName}`;

          newAchievementComponents.push({
            component: tui.asyncComponent(compPath),
            props: {
              userId: this.userId,
              assignmentId: this.assignmentId,
              type: path.type,
            },
          });
        });

        if (newAchievementComponents.length === 0) {
          this.$emit('loaded');
        }

        return newAchievementComponents;
      },
    },
  },

  methods: {
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.toLowerCase().slice(1);
    },
    itemLoaded() {
      this.itemsLoaded += 1;
    },
  },
};
</script>

<style lang="scss">
.tui-totaraCompetency-achievementPathDisplay {
  &__group {
    margin: 1em;
    padding: 1em;
    border: 1px solid black;
    border-radius: 6px;
  }
}
</style>
