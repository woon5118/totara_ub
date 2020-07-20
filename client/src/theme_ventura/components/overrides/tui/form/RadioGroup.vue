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
  --tui-form-radio-group-padding: var(--tui-gap-2);
  --tui-form-radio-group-spacing-v: var(--tui-gap-4);
  --tui-form-radio-group-spacing-h: var(--tui-gap-4);
}

.tui-radioGroup {
  display: flex;
  flex-direction: column;
  padding: var(--tui-form-radio-group-padding) 0;

  & > * + * {
    margin-top: var(--tui-form-radio-group-spacing-v);
  }

  &--inputSizedOptions {
    & > * + * {
      margin-top: var(--tui-form-radio-group-spacing-v);
    }

    & > * {
      align-items: center;
      min-height: var(--tui-form-input-height);
    }
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-radioGroup--horizontal {
    flex-direction: row;
    flex-wrap: wrap;
    // stylelint-disable length-zero-no-unit
    margin-top: calc(
      0px - var(--tui-form-radio-group-spacing-v) +
        var(--tui-form-radio-group-padding)
    );
    padding-top: 0;

    & > * {
      margin-top: var(--tui-form-radio-group-spacing-v);
      margin-right: var(--tui-form-radio-group-spacing-h);
    }
  }
}
</style>
