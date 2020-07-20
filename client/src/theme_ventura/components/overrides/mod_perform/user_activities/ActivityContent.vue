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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-participantContent {
  @include tui-font-body();

  &__header {
    margin: var(--tui-gap-6) 0 0;
    @include tui-font-heading-medium();
  }

  &__buttons {
    margin-top: var(--tui-gap-4);
  }

  &__user {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: var(--tui-gap-2) var(--tui-gap-4);
    border: var(--tui-border-width-thin) solid var(--tui-color-border);
    border-radius: var(--tui-border-radius-normal);

    &-relationship {
      display: flex;
      padding-top: var(--tui-gap-3);
    }

    &-relationshipValue {
      display: inline;
      margin: auto 0 auto var(--tui-gap-1);
      @include tui-font-heading-x-small();
    }
  }

  &__navigation {
    margin-top: var(--tui-gap-4);

    &-buttons {
      text-align: right;

      :not(:first-child) {
        margin-left: var(--tui-gap-4);
      }
    }
  }

  &__sectionHeading {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-6);

    &-title {
      flex: 1;
      margin: auto 0;
      @include tui-font-heading-small();
    }
  }

  &__sectionHeadingOtherResponsesBar {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding-top: var(--tui-gap-6);
    padding-bottom: var(--tui-gap-6);
    border-top: solid var(--tui-color-neutral-5) var(--tui-border-width-thin);
    border-bottom: solid var(--tui-color-neutral-5) var(--tui-border-width-thin);
  }

  &__sectionHeadingOtherResponsesDescription {
    margin: 0;
  }

  &__sectionHeading-other-response-switch {
    margin-top: var(--tui-gap-4);
    margin-left: 0;
  }

  &__section {
    &-required-container {
      margin-top: var(--tui-gap-2);
    }

    &-response-required {
      @include tui-font-heading-label();
      color: var(--tui-color-prompt-alert);
    }
  }

  &__sectionItem {
    margin-top: var(--tui-gap-4);

    &-content {
      & > * {
        margin-top: var(--tui-gap-4);
        margin-left: var(--tui-gap-12);
      }
    }
  }

  &__readonly {
    padding-top: var(--tui-gap-1);
  }

  &__response-optional {
    @include tui-font-body();
    @include tui-font-hint();
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-participantContent {
    // Spit the avatar and relationship blurb on tablet an larger.
    &__user {
      flex-direction: row;

      &-info {
        padding-right: var(--tui-gap-4);
      }

      &-relationship {
        display: block;
        padding-top: 0;
      }

      &-relationshipValue {
        display: block;
        margin: var(--tui-gap-1) 0 0;
      }
    }

    &__sectionHeadingOtherResponsesBar {
      flex-wrap: nowrap;
      margin-top: var(--tui-gap-6);
    }

    &__sectionHeading-other-response-switch {
      flex-shrink: 0;
      margin-top: 0;
      margin-left: var(--tui-gap-6);
    }
  }
}
</style>
