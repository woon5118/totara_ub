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
  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-competencyProfile {
  // disable scroll anchoring as it is problematic when switching between chart/list views
  overflow-anchor: none;

  &__split {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
  }

  &__titleSection {
    margin: var(--tui-gap-6) 0 var(--tui-gap-4) 0;
  }

  &__title {
    @include tui-font-heading-medium();
    margin: 0;
  }

  &__sectionTitle {
    @include tui-font-heading-small();
    margin: var(--tui-gap-4) 0 var(--tui-gap-2) 0;
  }

  &__filtersBar {
    margin: var(--tui-gap-6) 0;
  }
}
</style>
