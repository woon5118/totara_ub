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
    <component
      :is="component"
      :assignment-id="assignmentId"
      :user-id="userId"
      @loaded="isLoaded"
    />
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    assignmentId: {
      required: true,
      type: Number,
    },
    type: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      component: null,
    };
  },

  mounted: function() {
    let compPath = `pathway_${this.type}/components/achievements/AchievementDisplay`;
    this.component = tui.asyncComponent(compPath);
  },

  methods: {
    /**
     * Emit a 'loaded' event once the component emits an event saying it's ready
     *
     */
    isLoaded() {
      this.$emit('loaded');
    },
  },
};
</script>
