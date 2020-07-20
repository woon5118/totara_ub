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
.tui-formTextarea {
  display: block;
  flex-grow: 1;
  width: 100%;
  min-width: 0;
  max-width: 100%;
  max-height: 100%;
  margin: 0;
  padding: var(--tui-gap-1) var(--tui-gap-2);
  overflow: auto;
  color: var(--tui-form-input-text-color);
  font-size: var(--tui-form-input-font-size);
  font-family: inherit;
  line-height: inherit;
  border: var(--tui-form-input-border-size) solid
    var(--tui-form-input-border-color);
  resize: none;

  &::placeholder {
    color: var(--tui-form-input-text-placeholder-color);
  }

  &:focus {
    background: var(--tui-form-input-bg-color-focus);
    border: var(--tui-form-input-border-size) solid
      var(--tui-form-input-border-color-focus);
    outline: none;
    box-shadow: var(--tui-shadow-1);
  }

  &[disabled] {
    color: var(--tui-form-input-text-color-disabled);
    background: var(--tui-form-input-bg-color-disabled);
    border-color: var(--tui-form-input-border-color-disabled);

    &::placeholder {
      color: var(--tui-form-input-text-color-disabled);
    }
  }
}
</style>
