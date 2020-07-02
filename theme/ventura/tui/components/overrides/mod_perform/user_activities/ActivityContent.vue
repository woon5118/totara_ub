<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
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

// Spit the avatar and relationship blurb on tablet an larger.
@media (min-width: $tui-screen-xs) {
  .tui-participantContent {
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
  }
}
</style>
