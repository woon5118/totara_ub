<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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
.tui-formRow {
  display: flex;
  flex-direction: column;
  margin-bottom: var(--tui-gap-4);

  &__action {
    display: flex;
  }

  &__desc {
    display: flex;
    min-width: 0;
    margin-bottom: var(--tui-gap-1);
    text-align: left;
    overflow-wrap: break-word;
  }

  .tui-formLabel,
  .tui-formHelpIcon {
    padding-top: var(--tui-gap-1);
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-form--horizontal .tui-formRow {
    flex-direction: row;

    &__action {
      flex-grow: 1;
      min-width: 0;
      max-width: 400px;
    }

    &__desc {
      flex-shrink: 0;
      width: 40%;
      max-width: 164px;
      margin: 0 var(--tui-gap-4) 0 0;
    }
  }

  .tui-form--inputWidth-limited .tui-formRow {
    input[type='email'].tui-formInput,
    input[type='number'].tui-formInput,
    input[type='password'].tui-formInput,
    input[type='search'].tui-formInput,
    input[type='tel'].tui-formInput,
    input[type='text'].tui-formInput,
    input[type='url'].tui-formInput,
    .tui-select,
    .tui-formTextarea,
    .tui-formFieldError {
      width: 280px;
      max-width: 280px;
    }

    .tui-formField {
      flex-grow: 0;
    }
  }
}
</style>
