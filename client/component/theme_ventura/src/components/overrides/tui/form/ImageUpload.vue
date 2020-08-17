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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-formImageUpload {
  color: var(--tui-form-input-text-color);
  font-size: var(--tui-form-input-font-size);
  line-height: 1;
  background: var(--tui-form-input-bg-color);

  &__actions {
    display: flex;
    padding: var(--tui-form-input-v-padding) var(--tui-gap-2);
    background: var(--tui-color-neutral-3);
    border-color: var(--tui-form-input-border-color);
    border-top: var(--tui-form-input-border-size) solid;
    border-right: var(--tui-form-input-border-size) solid;
    border-left: var(--tui-form-input-border-size) solid;
  }
  &__deleteButton {
    margin-left: auto;
  }
  &__filearea {
    padding: var(--tui-form-input-v-padding) var(--tui-gap-2);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-input-border-color);

    .tui-formImageUpload--highlight & {
      background: var(--tui-form-input-bg-color-focus);
      border-color: var(--tui-form-input-border-color-focus);
      outline: none;
      box-shadow: var(--tui-form-input-shadow-focus);
    }
  }
  &__display {
    .tui-responsiveImage {
      max-height: 25rem;
    }
  }
  &__progress {
    padding: var(--tui-form-input-v-padding) var(--tui-gap-2);
  }
}
</style>
