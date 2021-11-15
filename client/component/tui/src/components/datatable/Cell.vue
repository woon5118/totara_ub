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
  <div
    class="tui-dataTableCell"
    :class="[
      align && 'tui-dataTableCell--align-' + align,
      heavy && 'tui-dataTableCell--heavy',
      repeatedHeader && 'tui-dataTableCell--repeatedHeader',
      size && 'tui-dataTableCell--size_' + size,
      valign && 'tui-dataTableCell--valign-' + valign,
    ]"
    role="cell"
  >
    <span
      v-if="columnHeader"
      class="tui-dataTableCell__label"
      aria-hidden="true"
    >
      {{ columnHeader }}
    </span>
    <div class="tui-dataTableCell__content">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    align: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
    columnHeader: String,
    heavy: Boolean,
    repeatedHeader: Boolean,
    size: String,
    valign: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
  },
};
</script>

<style lang="scss">
.tui-dataTableCell {
  /* ensure excessively long words don't push out cell width */
  min-width: 0;
  /* ensure excessively long words don't overflow */
  word-wrap: break-word;

  &__label {
    display: inline-block;
    margin-top: var(--gap-2);
    padding: var(--gap-1) 0 var(--gap-2);
    @include tui-font-heading-label-small();
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableCell {
    display: flex;
    flex-basis: 0;
    flex-direction: column;
    flex-grow: 1;

    &__label {
      display: none;
    }

    &--repeatedHeader &__content {
      @include sr-only();
    }

    &--size {
      &_1 {
        flex-grow: 1;
      }
      &_2 {
        flex-grow: 2;
      }
      &_3 {
        flex-grow: 3;
      }
      &_4 {
        flex-grow: 4;
      }
      &_5 {
        flex-grow: 5;
      }
      &_6 {
        flex-grow: 6;
      }
      &_7 {
        flex-grow: 7;
      }
      &_8 {
        flex-grow: 8;
      }
      &_9 {
        flex-grow: 9;
      }
      &_10 {
        flex-grow: 10;
      }
      &_11 {
        flex-grow: 11;
      }
      &_12 {
        flex-grow: 12;
      }
      &_13 {
        flex-grow: 13;
      }
      &_14 {
        flex-grow: 14;
      }
      &_15 {
        flex-grow: 15;
      }
      &_16 {
        flex-grow: 16;
      }
    }

    &--align {
      &-center {
        align-items: center;
      }

      &-end {
        align-items: flex-end;
      }
    }

    &--heavy {
      @include tui-font-heavy();
    }

    &--valign {
      &-center {
        justify-content: center;
      }

      &-end {
        justify-content: flex-end;
      }
    }
  }
}
</style>
