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
    <Arrow size="large" side="top" :distance="32" />
    <div class="tui-dataTableExpandableRow__placement">
      <div class="tui-dataTableExpandableRow__close">
        <CloseButton @click="$emit('close', $event.target)" />
      </div>

      <div class="tui-dataTableExpandableRow__content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
import Arrow from 'tui/components/decor/Arrow';
import CloseButton from 'tui/components/buttons/CloseIcon';

export default {
  components: {
    Arrow,
    CloseButton,
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "closebuttontitle"
  ]
}
</lang-strings>

<style lang="scss">
.tui-dataTableExpandableRow {
  position: relative;
  color: var(--datatable-expanded-text-color);
  border: 1px solid var(--datatable-expanded-border-color);
  box-shadow: var(--shadow-2);

  &__close {
    position: absolute;
    top: var(--gap-2);
    right: 0;
    z-index: 2;
    font-size: var(--font-size-20);

    .flex_icon {
      vertical-align: text-top;
    }
  }

  &__content {
    position: relative;
    height: 100vh;
    padding: var(--gap-3);
    overflow-x: hidden;
    overflow-y: scroll;
    background: var(--datatable-expanded-bg-color);
  }

  &__placement {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 0;
    padding: 0;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableExpandableRow {
    margin: var(--gap-2) 0;

    &__content {
      height: inherit;
      overflow-y: hidden;
    }

    &__placement {
      position: relative;
      top: inherit;
      left: 0;
      z-index: initial;
      width: 100%;
      height: inherit;
    }
  }
}
</style>
