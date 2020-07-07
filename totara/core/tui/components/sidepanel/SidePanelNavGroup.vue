<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-sidePanelNavGroup">
    <div v-if="title" class="tui-sidePanelNavGroup__heading">
      <h4
        :id="$id('side-panel-nav-group-heading-title')"
        class="tui-sidePanelNavGroup__heading-title"
      >
        {{ title }}
      </h4>

      <div class="tui-sidePanelNavGroup__heading-side">
        <slot name="heading-side" />
      </div>
    </div>
    <ul
      class="tui-sidePanelNavGroup__items"
      :aria-labelledby="
        title ? $id('side-panel-nav-group-heading-title') : null
      "
    >
      <PropsProvider :provide="provide">
        <slot />
      </PropsProvider>
    </ul>
  </div>
</template>

<script>
import PropsProvider from 'totara_core/components/util/PropsProvider';

export default {
  components: {
    PropsProvider,
  },

  props: {
    active: [Boolean, Number, String],
    title: [Boolean, String],
  },

  methods: {
    provide() {
      return {
        props: {
          active: this.active,
        },
        listeners: {
          select: this.$_handleSelect,
        },
      };
    },

    $_handleSelect(value) {
      this.$emit('select', value);
    },
  },
};
</script>
