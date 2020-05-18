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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.mod-perform-activitySection {
  &__participant-grid {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    margin-bottom: var(--tui-gap-4);
  }

  &__participant-grid-item {
    flex-basis: 100%;
  }

  @media (min-width: $tui-screen-xs) {
    &__participant-grid {
      flex-direction: row;
      margin-bottom: 0;
    }

    &__participant-name {
      max-width: 500px;
      margin-bottom: 0;
    }

    &__participant-options {
      margin-bottom: var(--tui-gap-2);
      padding: var(--tui-gap-2);
    }
  }

  &__participant-name {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--tui-gap-2);
    padding: var(--tui-gap-2);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: var(--tui-border-radius-normal);
  }

  &__participant-options {
    margin-top: 0;
    margin-bottom: var(--tui-gap-4);
  }
}
</style>
