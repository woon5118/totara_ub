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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-competencyDetailAssignment {
  &__bar {
    padding: var(--tui-gap-4);
    background: var(--tui-color-neutral-3);
  }

  &__actions {
    padding: var(--tui-gap-4);
  }

  .tui-grid--stacked {
    & > * + * {
      justify-content: flex-start;
      padding-top: var(--tui-gap-4);
    }
  }

  &__level {
    display: flex;
    align-items: center;

    &-header {
      display: flex;
      margin: 0;
      @include tui-font-body-small();
    }

    &-infoBtn {
      padding-left: var(--tui-gap-1);
    }

    &-text {
      @include tui-font-heading-label-small();
    }

    &-wrap {
      @include tui-font-body-small();
      padding-left: var(--tui-gap-2);
      border-style: solid;
      border-width: 0 0 0 var(--tui-border-width-thick);

      .dir-rtl & {
        padding-right: var(--tui-gap-2);
        border-width: 0 var(--tui-border-width-thick) 0 0;
      }

      &-pending {
        border-color: var(--tui-color-neutral-5);
      }

      &-complete {
        border-color: var(--tui-color-secondary);
      }

      &-achieved {
        border-color: var(--tui-color-primary);
      }
    }
  }

  &__status {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    &-text {
      margin-left: var(--tui-gap-2);
      @include tui-font-heading-small();

      .dir-rtl & {
        margin: 0 var(--tui-gap-2) 0 0;
      }

      &-complete {
        margin-left: var(--tui-gap-1);

        .dir-rtl & {
          margin: 0 var(--tui-gap-1) 0 0;
        }
      }
    }
  }
}
</style>
