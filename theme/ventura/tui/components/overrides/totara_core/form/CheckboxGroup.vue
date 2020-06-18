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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  --tui-form-checkbox-group-padding: var(--tui-gap-2);
  --tui-form-checkbox-group-spacing-v: var(--tui-gap-4);
  --tui-form-checkbox-group-spacing-h: var(--tui-gap-4);
}

.tui-checkboxGroup {
  display: flex;
  flex-direction: column;
  padding: var(--tui-form-checkbox-group-padding) 0;

  & > * + * {
    margin-top: var(--tui-form-checkbox-group-spacing-v);
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-checkboxGroup--horizontal {
    flex-direction: row;
    flex-wrap: wrap;
    // stylelint-disable length-zero-no-unit
    margin-top: calc(
      0px - var(--tui-form-checkbox-group-spacing-v) +
        var(--tui-form-checkbox-group-padding)
    );
    padding-top: 0;

    & > * {
      margin-top: var(--tui-form-checkbox-group-spacing-v);
      margin-right: var(--tui-form-checkbox-group-spacing-h);
    }
  }
}
</style>
