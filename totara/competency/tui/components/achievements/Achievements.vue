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
  @package totara_competency
-->

<template>
  <div class="tui-competencyAchievements">
    <Loader :loading="loading">
      <h4 class="tui-competencyAchievements__title">
        {{ $str('ways_to_achieve_proficiency', 'totara_competency') }}
      </h4>

      <!-- Output tabs showing paths to achieve proficiency (e.g. manual rating, criteria, leraing plan) -->
      <Tabs
        v-if="achievements.length"
        v-show="!loading"
        :key="assignmentId"
        class="tui-competencyAchievements__tabs"
      >
        <Tab
          v-for="(component, id) in achievements"
          :id="id"
          :key="id"
          :always-render="true"
          :name="component.props.name"
        >
          <!-- call either SingleValue or MultiValue component -->
          <component
            :is="component.component"
            v-bind="component.props"
            @loaded="itemLoaded"
          />
        </Tab>
      </Tabs>
    </Loader>
  </div>
</template>

<script>
// Components
import Loader from 'totara_core/components/loader/Loader';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';
// GraphQL
import AchievementPathsQuery from 'totara_competency/graphql/achievement_paths';

export default {
  components: {
    Loader,
    Tab,
    Tabs,
  },

  props: {
    assignment: {
      required: true,
      type: Object,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      achievements: [],
      itemsLoaded: null,
      loading: true,
    };
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
          let compPath = `totara_competency/components/achievements/${componentName}`;

          newAchievementComponents.push({
            component: tui.asyncComponent(compPath),
            props: {
              userId: this.userId,
              assignmentId: this.assignmentId,
              type: path.type,
              name: path.name,
            },
          });
        });

        // Make sure event is fired even if there are no items
        if (newAchievementComponents.length === 0) {
          this.$emit('loaded');
        }

        return newAchievementComponents;
      },
    },
  },

  computed: {
    /**
     * Convert assignmentId to number
     *
     * @return {Int}
     */
    assignmentId() {
      return parseInt(this.assignment.id);
    },

    /**
     * Return number of items
     *
     * @return {Int}
     */
    numberOfItems() {
      return this.achievements.length;
    },
  },

  watch: {
    /**
     * Check if all items are loaded, emit a 'loaded' event if they are
     *
     * @param {Object} loadedItems
     */
    itemsLoaded: function(loadedItems) {
      if (loadedItems === this.numberOfItems) {
        this.loading = false;
        this.$emit('loaded');
      }
    },

    /**
     * Don't display loader on assignment ID change
     *
     */
    assignmentId: function() {
      this.loading = false;
    },
  },

  methods: {
    /**
     * Convert fetched component name string to align with Vue naming convention
     *
     * @param {String} string
     * @return {String}
     */
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.toLowerCase().slice(1);
    },

    /**
     * Increments number of items loaded
     *
     */
    itemLoaded() {
      this.itemsLoaded += 1;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency" : [
      "ways_to_achieve_proficiency"
    ]
  }
</lang-strings>
