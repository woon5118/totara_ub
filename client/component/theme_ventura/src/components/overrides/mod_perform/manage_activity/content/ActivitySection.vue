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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performActivitySection {
  &--editing {
    padding: var(--tui-gap-4);
    border: solid var(--tui-color-secondary) var(--tui-border-width-normal);
  }

  &__title {
    margin: 0;
  }

  &__multiple {
    padding: var(--tui-gap-4);
    & > * + * {
      margin-top: var(--tui-gap-6);
    }
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

  &__divider {
    margin-top: var(--tui-gap-8);
    margin-bottom: 0;
  }

  &__content {
    margin-top: var(--tui-gap-4);

    &--autoSave {
      margin-top: var(--tui-gap-4);
      border-bottom: var(--tui-border-width-thin) solid
        var(--tui-card-border-color);
    }
  }

  &__can-view-others-legend {
    display: block;
  }

  &__participant-heading {
    margin-top: var(--tui-gap-2);
  }

  &__participant-items {
    & > * + * {
      margin-top: var(--tui-gap-2);
    }
  }

  &__participant-groups {
    margin-top: var(--tui-gap-2);
  }

  &__participant-group {
    & > * + * {
      margin-top: var(--tui-gap-4);
    }
  }

  &__participant-info {
    font-style: italic;
  }

  &__participant-heading {
    display: inline-block;
    margin-top: var(--tui-gap-4);
    margin-bottom: 0;
    @include tui-font-heading-label();
  }
}

@media (min-width: $tui-screen-sm) {
  .tui-performActivitySection {
    &__can-view-others-legend {
      display: inline;
    }

    &__content-buttons {
      display: flex;
      justify-content: flex-end;
    }
  }
}
</style>
