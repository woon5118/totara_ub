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
  @module totara_core
-->

<template>
  <div class="tui-collapsible">
    <div class="tui-collapsible__header">
      <ButtonIcon
        class="tui-collapsible__header_icons"
        :styleclass="{
          transparent: true,
        }"
        :aria-expanded="expanded.toString()"
        :aria-controls="generatedId + 'region'"
        :aria-label="label"
        @click="toggleExpand()"
      >
        <CollapseIcon v-if="expanded" size="100" />
        <ExpandIcon v-else size="100" />
      </ButtonIcon>
      <h3 :id="generatedId + 'label'" class="tui-collapsible__header-text">
        {{ label }}
        <slot name="label-extra" />
      </h3>

      <div class="tui-collapsible__header-sideContent">
        <slot name="collapsible-side-content" />
      </div>
    </div>
    <div
      v-if="alwaysRender || (expanded && $scopedSlots.default)"
      v-show="!alwaysRender || (expanded && $scopedSlots.default)"
      :id="generatedId + 'region'"
      class="tui-collapsible__content"
      role="region"
      :aria-labelledby="generatedId + 'label'"
    >
      <slot />
    </div>
  </div>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import CollapseIcon from 'tui/components/icons/common/Collapse';
import ExpandIcon from 'tui/components/icons/common/Expand';

export default {
  components: {
    ButtonIcon,
    CollapseIcon,
    ExpandIcon,
  },

  props: {
    alwaysRender: Boolean,
    id: {
      type: [String, Number],
    },
    label: {
      required: true,
      type: String,
    },
    initialState: {
      default: false,
      type: Boolean,
    },
    value: {
      default: undefined,
      type: Boolean,
    },
  },

  data() {
    return {
      state: this.initialState,
    };
  },

  computed: {
    /**
     * Update expand state base on value or internal state
     *
     * @return {Bool}
     */
    expanded() {
      // If no value prop provided use internal state
      if (this.value === undefined) {
        return this.state;
      }
      return this.value;
    },

    /**
     * Provide ID for accessibility tags
     *
     * @return {Bool}
     */
    generatedId() {
      return this.id || this.$id();
    },
  },

  methods: {
    // Toggle expanded state
    toggleExpand() {
      // If no value prop provided toggle internal state
      if (this.value === undefined) {
        this.state = !this.state;
        return;
      }
      // Propagate expanded value change to parent
      this.$emit('input', !this.value);
    },
  },
};
</script>
