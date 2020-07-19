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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  // Chart default font size
  --tui-font-size-chart-default: 14; // It has to be without units
  --tui-font-size-chart-doughnut-label: 16; // It has to be without units
  --tui-font-size-chart-doughnut-percentage-label: 30; // It has to be without units
}

.tui-chartJs__header {
  @include tui-font-heading-small();
  padding-bottom: var(--tui-gap-7);
  overflow: hidden;
  white-space: nowrap;
  text-align: center;
  text-overflow: ellipsis;
}
</style>
