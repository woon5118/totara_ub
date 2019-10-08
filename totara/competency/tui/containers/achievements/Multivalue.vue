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
      :user-id="userId"
      :assignment-id="assignmentId"
      @loaded="isLoaded"
    />
  </div>
</template>

<script>
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
    type: {
      required: true,
      type: String,
    },
  },

  data: function() {
    return {
      component: null,
    };
  },

  mounted: function() {
    let compPath = `pathway_${this.type}/containers/AchievementDisplay`;
    this.component = tui.asyncComponent(compPath);
  },

  methods: {
    isLoaded() {
      this.$emit('loaded');
    },
  },
};
</script>
