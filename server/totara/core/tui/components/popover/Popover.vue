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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div>
    <PopoverTrigger
      v-if="$scopedSlots.trigger"
      ref="trigger"
      :triggers="triggers"
      :ui-element="$refs.frame"
      @open-changed="setOpen"
    >
      <slot name="trigger" :is-open="isOpen" />
    </PopoverTrigger>
    <PopoverPositioner
      v-slot="{ side, arrowDistance }"
      :position="position"
      :open="isOpen"
      :reference-element="getReference()"
    >
      <PopoverFrame
        ref="frame"
        :title="title"
        :side="side"
        :arrow-distance="arrowDistance"
        @close="handleClose"
      >
        <slot :close="handleClose" />
        <template v-if="$scopedSlots.buttons" v-slot:buttons>
          <slot name="buttons" :close="handleClose" />
        </template>
      </PopoverFrame>
    </PopoverPositioner>
  </div>
</template>

<script>
import PopoverFrame from 'totara_core/components/popover/PopoverFrame';
import PopoverTrigger from 'totara_core/components/popover/PopoverTrigger';
import PopoverPositioner from 'totara_core/components/popover/PopoverPositioner';

const validTriggers = ['click', 'click-toggle', 'hover', 'focus'];
const validPositions = ['top', 'right', 'bottom', 'left'];

export default {
  components: {
    PopoverFrame,
    PopoverTrigger,
    PopoverPositioner,
  },

  props: {
    // note: this cannot be changed after a component is created.
    // only the initial trigger set will be used.
    triggers: {
      type: Array,
      default: () => ['click'],
      validator: value =>
        value === null || value.every(x => validTriggers.includes(x)),
    },
    title: String,
    reference: Object,
    position: {
      type: String,
      default: 'bottom',
      validator: x => x.split('-', 2).every(y => validPositions.includes(y)),
    },
    open: Boolean,
  },

  data() {
    return {
      isOpen: !!this.open,
      referenceElement: null,
    };
  },

  watch: {
    open() {
      this.isOpen = !!this.open;
    },
  },

  methods: {
    /**
     * Set whether the popover is open
     *
     * @param {boolean} visible
     */
    setOpen(visible) {
      this.isOpen = visible;
      this.$emit('open-changed', visible);
    },

    /**
     * Get the element to position the popover relative to.
     *
     * @returns {Element}
     */
    getReference() {
      let reference = this.reference || this.$refs.trigger;
      if (reference && reference.$el) {
        reference = reference.$el;
      }
      if (reference instanceof Element) {
        return reference;
      }
      return null;
    },

    handleClose() {
      if (!this.$refs.trigger) {
        this.$emit('request-close');
      } else {
        this.$refs.trigger.close();
      }
    },
  },
};
</script>
