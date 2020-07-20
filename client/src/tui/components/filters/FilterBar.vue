<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <section
    class="tui-filterBar"
    :class="{
      'tui-filterBar--hasTop': hasTopBar,
      'tui-filterBar--hasBottom': hasBottomBar,
    }"
  >
    <div class="tui-filterBar__heading">
      <h2 class="tui-filterBar__heading-header">
        {{ title }}
      </h2>

      <div
        v-if="Object.entries(value).length"
        class="tui-filterBar__heading-status"
        role="status"
      >
        {{
          $str(
            activeCount == 1
              ? 'a11y_active_filter_type'
              : 'a11y_active_filter_type_plural',
            'totara_core',
            activeCount
          )
        }}
      </div>
    </div>

    <div class="tui-filterBar__toggle">
      <ButtonIcon
        v-show="vertical"
        :aria-label="false"
        class="tui-filterBar__toggle-btn"
        :styleclass="{
          transparent: true,
        }"
        :text="
          $str(showFilters ? 'hide_filters' : 'show_filters', 'totara_core')
        "
        @click="toggleFilters"
      >
        <SliderIcon />
      </ButtonIcon>
    </div>

    <OverflowDetector
      v-if="showFilters || !vertical"
      v-slot="{ measuring }"
      @change="overflowChanged"
    >
      <div
        class="tui-filterBar__filters"
        :class="{
          'tui-filterBar__filters--stacked': vertical && !measuring,
        }"
      >
        <div class="tui-filterBar__filters-left">
          <div
            v-show="!vertical && !measuring"
            class="tui-filterBar__filters-icon"
            aria-hidden="true"
          >
            <SliderIcon
              custom-class="tui-filterBar__filters-iconSlider"
              :size="300"
              :title="title"
            />
          </div>
          <!-- Left aligned content -->
          <slot
            name="filters-left"
            :filters="value"
            :stacked="vertical && !measuring"
          />
        </div>
        <div class="tui-filterBar__filters-right">
          <!-- Right aligned content -->
          <slot
            name="filters-right"
            :filters="value"
            :stacked="vertical && !measuring"
          />
        </div>
      </div>
    </OverflowDetector>
  </section>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import OverflowDetector from 'tui/components/util/OverflowDetector';
import SliderIcon from 'tui/components/icons/common/Slider';

export default {
  components: {
    ButtonIcon,
    OverflowDetector,
    SliderIcon,
  },

  props: {
    title: {
      type: String,
      required: true,
    },
    value: {
      default() {
        return {};
      },
      type: Object,
    },
    hasTopBar: {
      type: Boolean,
      default: true,
    },
    hasBottomBar: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      showFilters: false,
      vertical: false,
    };
  },

  computed: {
    /**
     * Calculate the number of active filters
     *
     * @return {Int}
     */
    activeCount() {
      let count = 0;
      Object.keys(this.value).forEach(key => {
        let val = this.value[key];

        if (val instanceof Array) {
          if (val.length !== 0) {
            count += val.length;
          }
        } else if (val !== '') {
          count++;
        }
      });
      return count;
    },
  },

  watch: {
    activeCount: {
      handler(value) {
        this.$emit('active-count-changed', value);
      },
      immediate: true,
    },
  },

  methods: {
    /**
     * Switch vertical Bool to true when content is overflowing
     */
    overflowChanged({ overflowing }) {
      this.vertical = overflowing;
    },

    /**
     * Toggle visibility of filters on mobile
     *
     */
    toggleFilters() {
      this.showFilters = !this.showFilters;
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "a11y_active_filter_type",
    "a11y_active_filter_type_plural",
    "hide_filters",
    "show_filters"
  ]
}
</lang-strings>
