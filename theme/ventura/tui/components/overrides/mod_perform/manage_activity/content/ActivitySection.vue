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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performActivitySection {
  &__title {
    margin: 0;
  }

  &__multiple {
    padding: var(--tui-gap-4);
    & > * + * {
      margin-top: var(--tui-gap-4);
    }
  }

  &__editing {
    padding: var(--tui-gap-4);
    border: solid var(--tui-color-secondary) var(--tui-border-width-normal);
  }

  &.tui-card {
    display: block;
  }

  &__action-buttons {
    display: flex;
    justify-content: flex-end;
    .tui-iconBtn {
      min-width: auto;
    }
  }
  &__saveButtons {
    display: flex;
    justify-content: flex-end;
  }

  &__action-edit {
    min-width: auto;
  }

  &__content {
    padding: var(--tui-gap-4) 0;
    border-top: var(--tui-border-width-thin) solid var(--tui-card-border-color);

    &-autoSave {
      margin-top: var(--tui-gap-4);
      border-bottom: var(--tui-border-width-thin) solid
        var(--tui-card-border-color);
    }
  }

  &__participant {
    & > * {
      margin: var(--tui-gap-4) 0 0;
    }

    &-info {
      font-style: italic;
    }

    &-heading {
      @include tui-font-heading-label();
      display: inline-block;
    }
  }
}

@media (min-width: $tui-screen-lg) {
  .tui-performActivitySection {
    &__content-buttons {
      text-align: right;
    }
  }
}
</style>
