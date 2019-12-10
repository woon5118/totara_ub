<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-dropdown" :class="interactiveClasses">
    <div
      v-if="$scopedSlots.trigger"
      :id="$id('trigger')"
      ref="trigger"
      class="tui-dropdown__trigger"
    >
      <slot name="trigger" :toggle="toggle" :isOpen="isOpen" />
    </div>

    <transition :name="'tui-dropdown__animation-' + animation">
      <div
        v-show="!disabled && isOpen"
        ref="dropdownMenu"
        class="tui-dropdown__menu"
        :aria-hidden="!isOpen"
        :aria-labelledby="$id('trigger')"
        :role="role"
      >
        <div
          ref="dropdownContent"
          class="tui-dropdown__content"
          :class="{
            'tui-dropdown__content--separator': separator,
          }"
        >
          <PropsProvider :provide="provide">
            <slot />
          </PropsProvider>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import PropsProvider from 'totara_core/components/util/PropsProvider';
import { getTabbableElements } from '../../js/dom/focus';

const DEFAULT_CLOSE_OPTIONS = ['escape', 'outside'];

export default {
  components: {
    PropsProvider,
  },
  props: {
    value: {
      type: [String, Number, Boolean, Object, Array, Function],
      default: null,
    },
    disabled: Boolean,
    role: {
      type: String,
      default: 'menu',
    },
    position: {
      type: String,
      validator(value) {
        return (
          ['top-right', 'top-left', 'bottom-left', 'bottom-right'].indexOf(
            value
          ) > -1
        );
      },
    },
    separator: {
      type: Boolean,
      default: true,
    },
    animation: {
      type: String,
      default: 'default',
    },
    multiple: Boolean,
    closeOnClick: {
      type: Boolean,
      default: true,
    },
    canClose: {
      type: [Array, Boolean],
      default: true,
    },
    open: Boolean,
  },
  data() {
    return {
      toggleOpen: false,
      activeNodeIndex: null,
    };
  },
  computed: {
    interactiveClasses() {
      return [
        this.position && 'tui-dropdown--' + this.position,
        {
          'tui-dropdown--disabled': this.disabled,
          'tui-dropdown--open': this.isOpen,
        },
      ];
    },
    cancelOptions() {
      return typeof this.canClose === 'boolean'
        ? this.canClose
          ? DEFAULT_CLOSE_OPTIONS
          : []
        : this.canClose;
    },
    isOpen() {
      return this.open || this.toggleOpen;
    },
  },
  watch: {
    isOpen: {
      handler() {
        if (this.isOpen) {
          document.addEventListener('keydown', this.$_keyPress);
          document.addEventListener('click', this.$_clickedOutside);
        } else {
          document.removeEventListener('keydown', this.$_keyPress);
          document.removeEventListener('click', this.$_clickedOutside);
          this.activeNodeIndex = null;
        }
      },
      immediate: true,
    },
  },
  beforeDestroy() {
    if (typeof document !== 'undefined') {
      document.removeEventListener('click', this.$_clickedOutside);
      document.removeEventListener('keydown', this.$_keyPress);
    }
  },
  methods: {
    provide() {
      return {
        props: {
          disabled: this.disabled,
        },
      };
    },

    /**
     * Close dropdown if clicked outside.
     */
    $_clickedOutside(event) {
      // work around bug in Bootstrap < 3.3.5: https://github.com/twbs/bootstrap/issues/16090
      if (event.target !== this.$refs.dropdownMenu) {
        if (this.cancelOptions.indexOf('outside') < 0) return;
        if (!this.$refs.trigger || !this.$refs.trigger.contains(event.target)) {
          if (!this.closeOnClick) {
            // not close after click when we set the closeOnClick prop to false
            if (this.$refs.dropdownMenu.contains(event.target)) {
              return;
            }
          }

          // return focus to the trigger if a dropdown item has focus.
          // check where the focus is to avoid returning focus to the trigger if
          // clicking on the item has placed focus elsewhere (e.g. a modal)
          if (
            this.$refs.dropdownMenu.contains(document.activeElement) ||
            // also handle the case where we click on something non-focusable
            // inside the dropdown (which just shifts focus to the body)
            (this.$refs.dropdownMenu.contains(event.target) &&
              document.activeElement == document.body)
          ) {
            this.$_focusTrigger();
          }
          this.dismiss();
        }
      }
    },

    /**
     * Focus the trigger.
     */
    $_focusTrigger() {
      if (!this.$refs.trigger) {
        return;
      }
      const tabbable = getTabbableElements(this.$refs.trigger)[0];
      if (tabbable) {
        tabbable.focus();
      }
    },

    /**
     * Keypress event that is bound to the document
     */
    $_keyPress(event) {
      if (event.key === 'Escape') {
        if (this.cancelOptions.indexOf('escape') < 0) return;
        this.$_focusTrigger();
        this.dismiss();
        return;
      }

      const contentNodeList = this.$refs.dropdownContent.children;
      const contentNodeCount = contentNodeList.length;
      switch (event.key) {
        case 'ArrowDown':
        case 'Down':
          event.preventDefault();
          if (this.activeNodeIndex === contentNodeCount - 1) break;

          this.activeNodeIndex =
            this.activeNodeIndex !== null ? this.activeNodeIndex + 1 : 0;
          if (contentNodeCount > 0) {
            contentNodeList[this.activeNodeIndex].focus();
          }
          break;
        case 'ArrowUp':
        case 'Up':
          event.preventDefault();
          if (!this.activeNodeIndex) break;

          this.activeNodeIndex -= 1;
          if (contentNodeCount > 0)
            contentNodeList[this.activeNodeIndex].focus();
          break;
        case 'Tab':
          if (this.isOpen) {
            this.dismiss();
          }
          break;
      }
    },

    dismiss() {
      this.toggleOpen = false;
      this.$emit('dismiss');
    },

    /**
     * Toggle dropdown if it's not disabled.
     */
    toggle() {
      if (this.disabled) return;

      if (!this.toggleOpen) {
        // if not active, toggle after the clickOutside event
        this.$nextTick(() => {
          const value = !this.toggleOpen;
          this.toggleOpen = value;
        });
      } else {
        this.toggleOpen = !this.toggleOpen;
      }
    },
  },
};
</script>
