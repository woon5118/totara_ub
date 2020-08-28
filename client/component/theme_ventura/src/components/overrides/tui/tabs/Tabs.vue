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
  @module theme_ventura
-->

<style lang="scss">
:root {
  --tui-tab-border-width: 1px;
  // Tab inner horizontal padding
  --tui-tab-h-padding: var(--tui-gap-6);
  // Tab inner vertical padding
  --tui-tab-v-padding: var(--tui-gap-3);
  // Size of Highlight
  --tui-tab-highlight-height: var(--tui-gap-1);
  // Add extra spacing for drop shadow to be displayed
  --tui-tab-shadow-offset: var(--tui-gap-3);
  // Tab small version inner horizontal padding
  --tui-tab-small-h-padding: var(--tui-gap-4);
  // Tab small version inner vertical padding
  --tui-tab-small-v-padding: var(--tui-gap-3);
}

.tui-tabs {
  $mod-horizontal: #{&}--horizontal;
  $mod-vertical: #{&}--vertical;

  &--vertical {
    display: flex;
    flex-direction: row;
  }

  &__selector {
    list-style: none;
  }

  &__tabs {
    display: flex;
    align-items: flex-end;
    margin: 0;
    padding: 0;

    #{$mod-horizontal} & {
      border-bottom: var(--tui-tab-border-width) solid;
      border-bottom-color: var(--tui-tabs-border-color);
    }

    #{$mod-vertical} & {
      border-right: var(--tui-tab-border-width) solid;
      border-right-color: var(--tui-tabs-border-color);
    }
  }

  #{$mod-vertical} &__tabs {
    flex-direction: column;
    align-items: stretch;
  }

  &__tab {
    display: block;
    overflow: hidden;
    pointer-events: none;

    #{$mod-horizontal} & {
      margin: calc(var(--tui-tab-shadow-offset) * -1);
      margin-bottom: calc(var(--tui-tab-border-width) * -1);
      padding: var(--tui-tab-shadow-offset);
      padding-bottom: var(--tui-tab-border-width);
    }

    #{$mod-vertical} & {
      max-width: 220px;
      margin-right: calc(var(--tui-tab-border-width) * -1);
      margin-bottom: calc(var(--tui-tab-shadow-offset) * -1);
      padding-right: var(--tui-tab-border-width);
      padding-bottom: var(--tui-tab-shadow-offset);
    }

    &--hidden {
      display: none;
    }
  }

  a&__link {
    @include tui-font-link-large();
    display: flex;
    padding: var(--tui-tab-v-padding) var(--tui-tab-h-padding);
    color: var(--tui-tabs-text-color);
    text-decoration: none;

    border: var(--tui-tab-border-width) solid;
    border-color: transparent;

    pointer-events: auto;

    &:hover {
      color: var(--tui-tabs-text-color-focus);
      background: var(--tui-tabs-bg-color-focus);
    }

    &:focus {
      color: var(--tui-tabs-text-color-focus);
      background: var(--tui-tabs-bg-color-focus);
      outline: dashed 1px var(--tui-color-state-focus);
      outline-offset: -0.75rem;
    }

    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-tabs-text-color-active);
      outline: none;
    }

    #{$mod-horizontal} & {
      margin-top: var(--tui-tab-highlight-height);
      // overlap edges to avoid double border
      margin-right: calc(var(--tui-tab-border-width) * -1);
      border-bottom: none;
    }

    #{$mod-vertical} & {
      // overlap edges to avoid double border
      margin-bottom: calc(var(--tui-tab-border-width) * -1);
      margin-left: var(--tui-tab-highlight-height);
      border-right: none;
    }
  }

  &__tab--disabled a&__link {
    color: var(--tui-tabs-text-color-disabled);
    cursor: default;
    pointer-events: none;
  }

  &__tab--active a&__link {
    position: relative;
    color: var(--tui-tabs-text-color-selected);
    background: var(--tui-tabs-bg-color-selected);

    #{$mod-horizontal} & {
      top: var(--tui-tab-border-width);
      padding-top: calc(var(--tui-tab-v-padding) - var(--tui-tab-border-width));
      padding-bottom: calc(
        var(--tui-tab-v-padding) + var(--tui-tab-border-width)
      );
      border-color: var(--tui-tabs-border-color);
      box-shadow: var(--tui-shadow-3);
    }

    #{$mod-vertical} & {
      left: var(--tui-tab-border-width);
      padding-right: calc(
        var(--tui-tab-v-padding) + var(--tui-tab-border-width)
      );
      padding-left: calc(
        var(--tui-tab-h-padding) - var(--tui-tab-border-width)
      );
      border-color: var(--tui-tabs-border-color);
      box-shadow: var(--tui-shadow-2);
    }

    &::after {
      position: absolute;
      background: var(--tui-tabs-selected-bar-color);
      content: '';

      #{$mod-horizontal} & {
        top: calc(var(--tui-tab-highlight-height) * -1);
        right: 0;
        left: -1px;
        width: calc(100% + 2px);
        height: var(--tui-tab-highlight-height);
      }

      #{$mod-vertical} & {
        top: 0;
        bottom: 0;
        left: calc(var(--tui-tab-border-width) * -2);
        width: var(--tui-tab-highlight-height);
        height: calc(100% + 1px);
      }
    }
  }

  &__tabLabel {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
}

.tui-tabs {
  $mod-horizontal: #{&}--horizontal;
  $block: #{&};

  // Small tab
  &__tab--small {
    #{$mod-horizontal} & {
      #{$block}__link {
        @include tui-font-body-small;
        padding: var(--tui-tab-small-v-padding) var(--tui-tab-small-h-padding);
      }
    }
  }

  // Active small tab
  &__tab--active&__tab--small {
    #{$mod-horizontal} & {
      #{$block}__link {
        padding-top: calc(
          var(--tui-tab-small-v-padding) - var(--tui-tab-border-width)
        );
        padding-bottom: calc(
          var(--tui-tab-small-v-padding) + var(--tui-tab-border-width)
        );
        color: var(--tui-tabs-text-color-selected);
      }
    }
  }

  // Disabled small tab
  &__tab--disabled&__tab--small {
    #{$mod-horizontal} & {
      #{$block}__link {
        color: var(--tui-tabs-text-color-disabled);
        cursor: default;
        pointer-events: none;
      }
    }
  }
}

.tui-tabContent {
  .tui-tabs--horizontal & {
    padding-top: var(--tui-gap-4);
  }

  .tui-tabs--vertical & {
    padding-left: var(--tui-gap-4);
  }
}
</style>
