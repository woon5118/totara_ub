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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module totara_engage
-->

<template>
  <div
    :class="['tui-navigationPanel', `tui-navigationPanel__${gridDirection}`]"
  >
    <div v-if="showMenuControl" class="tui-navigationPanel__selected">
      <h2 class="tui-navigationPanel__header">
        <Button
          :text="title"
          :caret="true"
          class="tui-navigationPanel__btn"
          :styleclass="{ transparent: true }"
          :aria-expanded="showMenu.toString()"
          @click="toggleMenu"
        />
      </h2>
    </div>
    <div
      v-show="showMenu"
      ref="navigationPanelMenu"
      class="tui-navigationPanel__menu"
    >
      <h2 class="sr-only">{{ $str('navigation', 'totara_engage') }}</h2>
      <!-- Sections -->
      <template v-if="!navPanelLoading">
        <template v-for="(section, i) in sections">
          <component
            :is="section.component"
            :key="i"
            :selected-id="selectedId"
            :values="values"
            :show-contribute="section.showcontribute"
          />
        </template>
      </template>
      <!-- /Sections -->
    </div>
  </div>
</template>

<script>
import tui from 'tui/tui';
import Button from 'tui/components/buttons/Button';
import Show from 'tui/components/icons/Show';
import EngageSection from 'totara_engage/components/sidepanel/navigation/EngageSection';

// Mixins
import NavigationMixin from 'totara_engage/mixins/navigation_mixin';

// GraphQL
import getNavigationPanelSections from 'totara_engage/graphql/navigation_panel_sections';

const has = Object.prototype.hasOwnProperty;

export default {
  components: {
    Button,
    Show,
    EngageSection,
  },

  mixins: [NavigationMixin],

  props: {
    title: {
      type: String,
      required: true,
    },
    units: {
      type: [String, Number],
      required: true,
    },
    gridDirection: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      navPanelLoading: 0,
      menuVisible: false,
    };
  },

  apollo: {
    sections: {
      query: getNavigationPanelSections,
      loadingKey: 'navPanelLoading',
      result({ data: { sections } }) {
        if (typeof sections !== 'undefined' && 0 < sections.length) {
          this.$_loadTuiComponents(sections);
        }
      },
    },
  },

  computed: {
    /**
     * Show menu control if NavigationPanel is taking up the whole row of the grid.
     */
    showMenuControl() {
      return this.gridDirection === 'vertical';
    },
    /**
     * If NavigationPanel is taking up the whole grid row then return current toggled state.
     */
    showMenu() {
      if (this.showMenuControl) {
        return this.menuVisible;
      }
      return true;
    },
  },

  methods: {
    /**
     *
     * @param {Array} items
     */
    $_loadTuiComponents(items) {
      items.forEach(({ component, tuicomponent }) => {
        if (!has.call(this.$options.components, component)) {
          this.$options.components[component] = tui.asyncComponent(
            tuicomponent
          );
        }
      });
    },

    /**
     * Show/Hide navigation menu.
     */
    toggleMenu() {
      this.menuVisible = !this.menuVisible;
    },
  },
};
</script>

<lang-strings>
{
  "totara_engage": [
    "navigation"
  ]
}
</lang-strings>
