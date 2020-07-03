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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package theme_ventura
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
      @include tui-font-body-x-small();
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
