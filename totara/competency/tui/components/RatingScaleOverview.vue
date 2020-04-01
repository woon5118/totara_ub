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
  <div class="tui-competencyRatingScaleOverview">
    <p
      :id="$id('rating-scale-title')"
      class="tui-competencyRatingScaleOverview__title"
    >
      {{ $str('rating_scale', 'totara_competency') }}
    </p>
    <ol
      class="tui-competencyRatingScaleOverview__list"
      :aria-labelledby="$id('rating-scale-title')"
    >
      <li
        v-for="scaleValue in scaleValues"
        :key="scaleValue.id"
        class="tui-competencyRatingScaleOverview__list-item"
      >
        <div class="tui-competencyRatingScaleOverview__proficientStatus">
          <CheckSuccess v-if="isMinProficientValue(scaleValue)" size="300" />
        </div>
        <div class="tui-competencyRatingScaleOverview__scaleValue">
          <p class="tui-competencyRatingScaleOverview__scaleValue-name">
            {{ scaleValue.name }}
          </p>
          <p
            v-if="showDescription(scaleValue.description)"
            class="tui-competencyRatingScaleOverview__scaleValue-description"
            v-html="scaleValue.description"
          />
          <span class="sr-only">
            {{
              scaleValue.proficient
                ? $str('is_proficient_value', 'totara_competency')
                : $str('is_not_proficient_value', 'totara_competency')
            }}
          </span>
        </div>
      </li>
    </ol>
  </div>
</template>

<script>
import CheckSuccess from 'totara_core/components/icons/common/CheckSuccess';

export default {
  components: { CheckSuccess },
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
  },

  computed: {
    /**
     * The values we want.
     * @returns {Array}
     */
    scaleValues() {
      if (this.reverseValues) {
        return this.scale.values.slice(0).reverse();
      } else {
        return this.scale.values;
      }
    },

    /**
     * Get the scale value that is the minimum required for the competency to be proficient.
     * @returns {Object}
     */
    minProficientValue() {
      return this.scaleValues
        .slice(0)
        .reverse()
        .find(({ proficient }) => proficient);
    },
  },

  methods: {
    /**
     * Do we show this description?
     * @param {String} description
     * @returns {boolean}
     */
    showDescription(description) {
      return (
        this.showDescriptions && description != null && description.length > 0
      );
    },

    /**
     * Is the specified value the minimum needed to be proficient?
     * Used to show the tick box.
     * @param {Object} value
     * @returns {boolean}
     */
    isMinProficientValue(value) {
      if (!this.minProficientValue) {
        return false;
      }

      return this.minProficientValue.id === value.id;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "is_not_proficient_value",
      "is_proficient_value",
      "rating_scale"
    ]
  }
</lang-strings>
