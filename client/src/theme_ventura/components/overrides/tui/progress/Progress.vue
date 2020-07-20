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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-progress {
  @include tui-font-body-xx-small();

  display: flex;
  height: 14px;
  background-color: var(--tui-progress-container-bg-color);
  border: 1px solid var(--tui-progress-border-color);
  border-radius: var(--tui-gap-2);

  &--small {
    height: 12px;
    line-height: 10px;
  }

  &--hideBackground {
    background: none;
  }

  &__bar {
    display: flex;
    justify-content: flex-end;
    background-color: var(--tui-progress-value-bg-color);
    border-radius: var(--tui-gap-2);
    transition: width var(--tui-transition-progress-function)
      var(--tui-transition-progress-duration);
  }

  &__label {
    padding: 0 var(--tui-gap-1);
    opacity: 0;

    &--inside {
      color: var(--tui-progress-value-text-color);
      opacity: 1;
      transition: opacity var(--tui-transition-progress-function)
        var(--tui-transition-progress-duration);
    }
    &--outside {
      color: var(--tui-progress-container-text-color);
      opacity: 1;
    }
  }
}
</style>
