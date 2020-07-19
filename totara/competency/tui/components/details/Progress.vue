<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_competency
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
