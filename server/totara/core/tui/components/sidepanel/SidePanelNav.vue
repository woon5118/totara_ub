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
  <nav class="tui-sidePanelNav" :aria-label="ariaLabel">
    <PropsProvider :provide="provide">
      <slot />
    </PropsProvider>
  </nav>
</template>

<script>
import PropsProvider from 'totara_core/components/util/PropsProvider';

export default {
  components: {
    PropsProvider,
  },

  props: {
    ariaLabel: [Boolean, String],
    value: [Boolean, Number, String],
  },

  methods: {
    provide() {
      return {
        props: {
          active: this.value,
        },
        listeners: {
          select: this.$_handleSelect,
        },
      };
    },

    $_handleSelect(selected) {
      this.$emit('input', selected.id);
      this.$emit('change', selected);
    },
  },
};
</script>
