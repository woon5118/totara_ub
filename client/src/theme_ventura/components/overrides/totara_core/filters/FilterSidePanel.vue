<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-filterSidePanel {
  display: block;
  padding: var(--tui-gap-4);

  &__header {
    @include tui-font-heading-x-small();
    min-width: 0;
    margin: 0 var(--tui-gap-2) 0 0;
  }

  &__heading {
    display: flex;
  }

  &__instructions,
  &__status {
    @include sr-only();
  }

  .tui-filterSidePanel__clearBtn {
    flex-shrink: 0;
    margin-left: auto;
  }
}

.tui-filterSidePanel.tui-filterSidePanel {
  & > * + * {
    margin-top: var(--tui-gap-4);
  }

  .tui-fieldset__legend,
  .tui-formLabel {
    @include tui-font-heading-label-small();
  }
}
</style>
