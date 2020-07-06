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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  --tui-avatar-border-radius: 50%;
  --tui-avatar-large-width: 185px;
  --tui-avatar-large-height: 185px;
  --tui-avatar-large-padding: 5px;
  --tui-avatar-medium-width: 105px;
  --tui-avatar-medium-height: 105px;
  --tui-avatar-medium-padding: 3px;
  --tui-avatar-small-width: 50px;
  --tui-avatar-small-height: 50px;
  --tui-avatar-small-padding: 2px;
  --tui-avatar-xsmall-width: 38px;
  --tui-avatar-xsmall-height: 38px;
  --tui-avatar-xsmall-padding: 1px;
  --tui-avatar-xxsmall-width: 24px;
  --tui-avatar-xxsmall-height: 24px;
  --tui-avatar-xxsmall-padding: 1px;
}

.tui-avatar {
  border: var(--tui-border-width-normal) solid var(--tui-avatar-border-color);
  border-radius: var(--tui-avatar-border-radius);

  &--large {
    width: var(--tui-avatar-large-width);
    height: var(--tui-avatar-large-height);
    padding: var(--tui-avatar-large-padding);
  }

  &--medium {
    width: var(--tui-avatar-medium-width);
    height: var(--tui-avatar-medium-height);
    padding: var(--tui-avatar-medium-padding);
  }

  &--small {
    width: var(--tui-avatar-small-width);
    height: var(--tui-avatar-small-height);
    padding: var(--tui-avatar-small-padding);
  }

  &--xsmall {
    width: var(--tui-avatar-xsmall-width);
    height: var(--tui-avatar-xsmall-height);
    padding: var(--tui-avatar-xsmall-padding);
  }

  &--xxsmall {
    width: var(--tui-avatar-xxsmall-width);
    height: var(--tui-avatar-xxsmall-height);
    padding: var(--tui-avatar-xxsmall-padding);
  }
}
</style>
