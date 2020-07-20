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
  @module theme_ventura
-->

<style lang="scss">
.tui-lozenge {
  display: inline-flex;
  padding: 0 var(--tui-gap-2) calc(var(--tui-gap-1) / 2);
  color: var(--tui-color-neutral-1);
  @include tui-font-heading-label-small();
  font-weight: 500;
  line-height: 1.2;
  background: var(--tui-color-prompt-info);
  border-radius: var(--tui-border-radius-small);

  &--alert {
    background: var(--tui-color-prompt-alert);
  }

  &--success {
    background: var(--tui-color-prompt-success);
  }

  &--warning {
    background: var(--tui-color-prompt-warning);
  }
}
</style>
