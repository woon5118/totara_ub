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
  --tui-form-checkbox-group-padding: var(--tui-gap-2);
  --tui-form-checkbox-group-spacing-v: var(--tui-gap-4);
  --tui-form-checkbox-group-spacing-h: var(--tui-gap-4);
}

.tui-checkboxGroup {
  display: flex;
  flex-direction: column;
  padding: var(--tui-form-checkbox-group-padding) 0;

  & > * + * {
    margin-top: var(--tui-form-checkbox-group-spacing-v);
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-checkboxGroup--horizontal {
    flex-direction: row;
    flex-wrap: wrap;
    // stylelint-disable length-zero-no-unit
    margin-top: calc(
      0px - var(--tui-form-checkbox-group-spacing-v) +
        var(--tui-form-checkbox-group-padding)
    );
    padding-top: 0;

    & > * {
      margin-top: var(--tui-form-checkbox-group-spacing-v);
      margin-right: var(--tui-form-checkbox-group-spacing-h);
    }
  }
}
</style>
