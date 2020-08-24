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
  @module tui
-->

<template>
  <div class="tui-dataTableExpandableRow" @keydown.esc="$emit('close')">
    <div class="tui-dataTableExpandableRow__placement">
      <div class="tui-dataTableExpandableRow__content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},
};
</script>

<style lang="scss">
.tui-dataTableExpandableRow {
  position: relative;
  margin: 0 0 var(--gap-2) calc(0px - var(--border-width-thin));
  color: var(--datatable-expanded-text-color);
  border: var(--border-width-thin) solid var(--datatable-expanded-border-color);
  border-top: none;
  box-shadow: var(--shadow-2);

  &__content {
    padding: var(--gap-3);
    background: var(--datatable-expanded-bg-color);
  }
}
</style>
