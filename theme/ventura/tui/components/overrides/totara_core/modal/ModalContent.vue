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
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  --tui-modal-content-separation: var(--tui-gap-2);
  --tui-modal-content-separation-large: var(--tui-gap-8);
  --tui-modal-content-outer-padding: var(--tui-gap-8);
}

.tui-modalContent {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-height: 0;
  padding: var(--tui-modal-content-outer-padding) 0;

  &__header {
    display: flex;
    flex-shrink: 0;
    padding: 0 var(--tui-modal-content-outer-padding);

    &-title {
      @include tui-font-heading-small();
      flex-grow: 1;
      overflow: hidden;
    }

    &-close {
      position: absolute;
      top: 0;
      right: 0;
      padding: var(--tui-gap-3);
    }
  }

  &__content {
    flex-grow: 1;
    min-height: 0;
    margin-top: var(--tui-modal-content-separation);
    padding: 0 var(--tui-modal-content-outer-padding);
    overflow-y: auto;
  }

  &__footer {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    margin-top: var(--tui-modal-content-separation-large);
    padding: 0 var(--tui-modal-content-outer-padding);

    &-buttons {
      margin-left: auto;
    }
  }
}
</style>
