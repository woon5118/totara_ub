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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyDetailProgress">
    <ProgressTracker
      :items="formattedForTracker"
      :current-id="myValue"
      :popover-trigger-type="['click']"
      :target-id="minProficientValueId"
    >
      <!-- Popover content -->
      <template v-slot:custom-popover-content="{ description, label, target }">
        <div class="tui-competencyDetailProgress__popover">
          <div
            v-if="target"
            class="tui-competencyDetailProgress__popover-target"
          >
            <Lozenge
              :text="$str('proficient_level', 'totara_competency')"
              type="success"
            />
          </div>

          <h5 class="tui-competencyDetailProgress__popover-header">
            {{ label }}
          </h5>

          <div
            v-if="description"
            class="tui-competencyDetailProgress__popover-body"
            v-html="description"
          />
          <div v-else class="tui-competencyDetailProgress__popover-body">
            {{ $str('no_description', 'totara_competency') }}
          </div>
        </div>
      </template>
    </ProgressTracker>
  </div>
</template>

<script>
// Components
import Lozenge from 'totara_core/components/lozenge/Lozenge';
import ProgressTracker from 'totara_core/components/progresstracker/ProgressTracker';

// GraphQL
import ScaleDetailsQuery from 'totara_competency/graphql/scale';

export default {
  components: {
    Lozenge,
    ProgressTracker,
  },
  props: {
    myValue: {
      type: [Number, String],
    },
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      scale: {},
    };
  },

  apollo: {
    scale: {
      query: ScaleDetailsQuery,
      context: { batch: true },
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update({ totara_competency_scale: scale }) {
        this.$emit('loaded');
        return scale;
      },
    },
  },

  computed: {
    /**
     * Provide ID for the minimum scale at which you can be classed proficient
     *
     * @return {Int}
     */
    minProficientValueId() {
      if (!this.scale.values) return null;
      return this.scale.values.find(({ proficient }) => proficient).id;
    },

    /**
     * Format scale values for progress tracker
     *
     * @return {Array}
     */
    formattedForTracker() {
      if (!this.scale.values) return [];
      return this.scale.values.map(function(elem) {
        return {
          description: elem.description,
          id: elem.id,
          label: elem.name,
        };
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "no_description",
      "proficient_level"
    ]
  }
</lang-strings>
