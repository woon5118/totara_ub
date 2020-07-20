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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <div
    class="tui-tabs"
    :class="['tui-tabs--' + direction]"
    :aria-orientation="direction"
  >
    <OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
      <ul role="tablist" class="tui-tabs__tabs">
        <li
          v-for="(tab, i) in tabs"
          :key="i"
          class="tui-tabs__tab"
          :class="{
            'tui-tabs__tab--active': tab.active,
            'tui-tabs__tab--disabled': tab.disabled,
            'tui-tabs__tab--small': smallTabs,
            'tui-tabs__tab--hidden': overflowing && !measuring,
          }"
          role="presentation"
        >
          <a
            :id="'tab-' + tab.htmlId"
            ref="tabs"
            :aria-selected="tab.active"
            :aria-disabled="tab.disabled"
            :aria-controls="tab.active ? 'tabpanel-' + tab.htmlId : false"
            href="#"
            class="tui-tabs__link"
            role="tab"
            :tabindex="tab.active ? false : -1"
            @click.prevent="selectTab(tab, $event)"
            @keydown="handleTabKeydown"
          >
            <span class="tui-tabs__tabLabel">{{ tab.name }}</span>
          </a>
        </li>

        <!-- Fallback select list when there isn't enough space -->
        <li v-if="overflowing && !measuring" class="tui-tabs__selector">
          <Form>
            <FormRow
              v-slot="{ id }"
              :label="$str('select_a_tab', 'totara_core')"
            >
              <Select
                :id="id"
                :value="currentSelected"
                :options="selectOptions"
                @input="selectTabId"
              />
            </FormRow>
          </Form>
        </li>
      </ul>
    </OverflowDetector>
    <div class="tui-tabs__panels">
      <PropsProvider :provide="provideSlot">
        <slot />
      </PropsProvider>
    </div>
  </div>
</template>

<script>
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import OverflowDetector from 'tui/components/util/OverflowDetector';
import PropsProvider from 'tui/components/util/PropsProvider';
import Select from 'tui/components/form/Select';

const events = {
  horizontal: {
    prev: ['Left', 'ArrowLeft'],
    next: ['Right', 'ArrowRight'],
  },
  vertical: {
    prev: ['Left', 'ArrowLeft', 'Up', 'ArrowUp'],
    next: ['Right', 'ArrowRight', 'Down', 'ArrowDown'],
  },
};

export default {
  components: {
    Form,
    FormRow,
    OverflowDetector,
    PropsProvider,
    Select,
  },

  model: {
    prop: 'selected',
    event: 'input',
  },

  props: {
    selected: [String, Number],
    direction: {
      type: String,
      default: 'horizontal',
      validator: x => ['horizontal', 'vertical'].includes(x),
    },
    smallTabs: {
      type: Boolean,
    },
  },

  data() {
    return {
      selectOptions: [],
      overflowing: false,
      tabs: [],
      currentSelected: this.selected,
    };
  },

  watch: {
    selected(value) {
      this.currentSelected = value;
    },
  },

  mounted() {
    // exclude child components that were defined in this component (overflowDetector)
    this.tabs = this.$children.filter(x => x.$vnode.context != this);
    this.selectListTabs();

    if (this.currentSelected == null) {
      this.currentSelected = this.$_tabIdFromProps(this.tabs[0]);
    }
  },

  updated() {
    // exclude child components that were defined in this component (overflowDetector)
    this.tabs = this.$children.filter(x => x.$vnode.context != this);
    this.selectListTabs();

    // check if focus is on a tab that is not selected, if so chg
    // use role[tab] and aria-selected to determine
    if (this.$refs.tabs.includes(document.activeElement)) {
      const currentSelectedEl = this.$refs.tabs.find(x =>
        x.getAttribute('aria-selected')
      );
      if (currentSelectedEl && currentSelectedEl != document.activeElement) {
        currentSelectedEl.focus();
      }
    }
  },

  methods: {
    provideSlot({ props }) {
      const id = this.$_tabIdFromProps(props);
      return {
        props: {
          active: this.currentSelected != null && this.currentSelected === id,
        },
      };
    },

    $_tabIdFromProps(comp) {
      return comp ? comp.id : null;
    },

    /**
     * Check if a clicked tab has been disabled, if not set it to the currently selected
     *
     * @param {Object} tab
     */
    selectTab(tab) {
      if (tab.disabled) {
        return;
      }
      this.$_setSelectedTab(tab);
    },

    selectTabId(id) {
      this.currentSelected = id;
      this.$emit('input', id);
    },

    handleTabKeydown(e) {
      const currentEvents = events[this.direction] || events.horizontal;
      if (currentEvents.prev.includes(e.key)) {
        e.preventDefault();
        this.navigateTabBy(-1);
      }
      if (currentEvents.next.includes(e.key)) {
        e.preventDefault();
        this.navigateTabBy(1);
      }
      if (e.key == 'Home') {
        e.preventDefault();
        this.$_setSelectedTab(this.tabs[0]);
      }
      if (e.key == 'End') {
        e.preventDefault();
        this.$_setSelectedTab(this.tabs[this.tabs.length - 1]);
      }
    },

    /**
     * Allow selected tab to be changed with left / right arrows
     *
     * @param {Integer} direction
     */
    navigateTabBy(direction) {
      direction = direction < 0 ? -1 : 1;

      let index = this.tabs.findIndex(
        x => this.$_tabIdFromProps(x) == this.currentSelected
      );

      if (index == -1) {
        // ensure index is within this.tabs otherwise we will get an infinite loop
        index = direction < 0 ? 0 : this.tabs.length - 1;
      }

      // find next non-disabled link
      let newIndex = index;
      do {
        newIndex += direction;
        if (newIndex < 0) {
          newIndex = this.tabs.length - 1;
        }
        if (newIndex >= this.tabs.length) {
          newIndex = 0;
        }
      } while (this.tabs[newIndex].disabled && newIndex != index);

      this.$_setSelectedTab(this.tabs[newIndex]);
    },

    /**
     * Create a list of tab options for a select list for when the tabs don't have enough space to display
     *
     * @return {Array}
     */
    selectListTabs() {
      this.selectOptions = this.tabs.map(tab => {
        return {
          id: tab.id,
          label: tab.name,
          disabled: tab.disabled,
        };
      });
    },

    /**
     * Set the currently selected tab ID
     *
     * @param {Object} tab
     */
    $_setSelectedTab(tab) {
      const id = this.$_tabIdFromProps(tab);
      this.currentSelected = id;
      this.$emit('input', id);
    },

    /**
     * Switch vertical Bool to true when content is overflowing
     */
    overflowChanged({ overflowing }) {
      if (this.direction === 'vertical') {
        return;
      }
      this.overflowing = overflowing;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "select_a_tab"
    ]
  }
</lang-strings>
