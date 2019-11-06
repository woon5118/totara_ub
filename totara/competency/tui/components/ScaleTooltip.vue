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
  @package pathway_manual
-->

<template>
  <Tooltip v-if="scale" :display="display">
    <div class="tui-totaraCompetency-scaleTooltip__title">
      {{ $str('rating_scale', 'totara_competency') }}
    </div>
    <div class="tui-totaraCompetency-scaleTooltip__table">
      <div
        v-for="scaleValue in scaleValues"
        :key="scaleValue.id"
        class="tui-totaraCompetency-scaleTooltip__table_row"
      >
        <div
          class="tui-totaraCompetency-scaleTooltip__table_cell tui-totaraCompetency-scaleTooltip__table_cell--icon"
        >
          <FlexIcon
            v-if="isMinProficientValue(scaleValue)"
            icon="check-circle-success"
            size="300"
          />
        </div>
        <div
          v-if="showDescription(scaleValue.description)"
          class="tui-totaraCompetency-scaleTooltip__table_cell tui-totaraCompetency-scaleTooltip--withDescription"
        >
          <div>{{ scaleValue.name }}</div>
          <div
            class="tui-totaraCompetency-scaleTooltip--withDescription_description"
            v-html="scaleValue.description"
          />
        </div>
        <div v-else class="tui-totaraCompetency-scaleTooltip__table_cell">
          {{ scaleValue.name }}
        </div>
      </div>
    </div>
  </Tooltip>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Tooltip from 'totara_competency/components/Tooltip';

export default {
  components: { FlexIcon, Tooltip },
  props: {
    scale: {
      required: true,
      type: Object,
    },
    display: {
      required: true,
      type: Boolean,
    },
    showDescriptions: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    scaleValues() {
      return this.scale.values.slice(0).reverse();
    },

    minProficientValue() {
      return this.scale.values.find(({ proficient }) => proficient);
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
.tui-totaraCompetency-scaleTooltip {
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
      max-width: 350px;
      padding-top: var(--tui-gap-1);
      padding-left: var(--tui-gap-2);
    }
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
