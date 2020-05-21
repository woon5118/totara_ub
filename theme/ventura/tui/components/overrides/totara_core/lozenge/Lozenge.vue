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
.tui-lozenge {
  display: inline-flex;
  padding: 0 var(--tui-gap-2) calc(var(--tui-gap-1) / 2);
  color: var(--tui-color-neutral-1);
  @include tui-font-heading-label-small();
  font-weight: 500;
  line-height: 1.2;
  background: var(--tui-color-prompt-info);
  border-radius: var(--tui-border-radius-small);

  &--alert {
    background: var(--tui-color-prompt-alert);
  }

  &--success {
    background: var(--tui-color-prompt-success);
  }

  &--warning {
    background: var(--tui-color-prompt-warning);
  }
}
</style>
