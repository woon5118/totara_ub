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
.tui-sharedBoard {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  margin-bottom: var(--tui-gap-2);

  &__label {
    display: inline-flex;
    margin-top: var(--tui-gap-2);
    @include tui-font-body-small();
  }
  &__content {
    flex-basis: 1px;
    flex-direction: column;
    flex-grow: 1;
    min-height: 60px;
    padding: var(--tui-gap-1);
    padding-left: 0;
    overflow-y: auto;

    &__container {
      display: flex;
      flex-wrap: wrap;
    }

    &__tags {
      margin: 0 0 var(--tui-gap-1) var(--tui-gap-1);
      list-style-type: none;
    }
  }
}
</style>
