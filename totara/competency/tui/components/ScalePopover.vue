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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <Popover v-if="scale" :triggers="triggers" :position="position">
    <template v-slot:trigger class="tui-totaraCompetency-scalePopover__inline">
      <slot />
    </template>
    <div class="tui-totaraCompetency-scalePopover__title">
      {{ $str('rating_scale', 'totara_competency') }}
    </div>
    <div class="tui-totaraCompetency-scalePopover__table">
      <div
        v-for="scaleValue in scaleValues"
        :key="scaleValue.id"
        class="tui-totaraCompetency-scalePopover__table_row"
      >
        <div
          class="tui-totaraCompetency-scalePopover__table_cell tui-totaraCompetency-scalePopover__table_cell--icon"
        >
          <FlexIcon
            v-if="isMinProficientValue(scaleValue)"
            icon="check-circle-success"
            size="300"
          />
        </div>
        <div
          v-if="showDescription(scaleValue.description)"
          class="tui-totaraCompetency-scalePopover__table_cell tui-totaraCompetency-scalePopover--withDescription"
        >
          <div>{{ scaleValue.name }}</div>
          <div
            class="tui-totaraCompetency-scalePopover--withDescription_description"
            v-html="scaleValue.description"
          />
        </div>
        <div v-else class="tui-totaraCompetency-scalePopover__table_cell">
          {{ scaleValue.name }}
        </div>
      </div>
    </div>
  </Popover>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Popover from 'totara_core/components/popover/Popover';

export default {
  components: { Popover, FlexIcon },
  props: {
    scale: {
      required: true,
      type: Object,
    },
    showDescriptions: {
      type: Boolean,
      default: false,
    },
    reverseValues: {
      type: Boolean,
      default: false,
    },
    triggers: {
      type: Array,
      default: () => ['click'],
    },
    position: {
      type: String,
      default: 'bottom',
    },
  },

  computed: {
    scaleValues() {
      if (this.reverseValues) {
        return this.scale.values.slice(0).reverse();
      } else {
        return this.scale.values;
      }
    },

    minProficientValue() {
      return this.scaleValues.find(({ proficient }) => proficient);
    },
  },

  methods: {
    showDescription(description) {
      return (
        this.showDescriptions && description != null && description.length > 0
      );
    },

    isMinProficientValue(value) {
      if (!this.minProficientValue) {
        return false;
      }

      return this.minProficientValue.id === value.id;
    },
  },
};
</script>

<style lang="scss">
.tui-totaraCompetency-scalePopover {
  &__title {
    margin-bottom: var(--tui-gap-1);
    font-weight: bold;
  }
  &__table {
    display: table;
    &_row {
      display: table-row;
    }
    &_cell {
      display: table-cell;
      padding: var(--tui-gap-1) 0;
      font-weight: normal;
      &:not(:first-child) {
        padding-left: var(--tui-gap-1);
      }
    }
  }
  &--withDescription {
    padding-bottom: var(--tui-gap-2);
    &_description {
      padding-top: var(--tui-gap-1);
      padding-left: var(--tui-gap-2);
    }
  }
  &__inline {
    display: inline;
  }
}
</style>

<lang-strings>
  {
    "totara_competency": [
      "rating_scale"
    ]
  }
</lang-strings>
