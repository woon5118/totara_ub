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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <Popover
    :triggers="['click']"
    :position="position"
    class="tui-totaraCompetency-helpPopover"
  >
    <template v-slot:trigger>
      <span class="tui-totaraCompetency-helpPopover__icon">
        <FlexIcon icon="info" size="200" :title="$str('help')" />
      </span>
    </template>
    <div>
      <slot />
    </div>
  </Popover>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Popover from 'totara_core/components/popover/Popover';

export default {
  components: {
    FlexIcon,
    Popover,
  },

  props: {
    position: {
      type: String,
      default: 'top',
    },
  },
};
</script>

<style lang="scss">
.tui-totaraCompetency-helpPopover {
  display: inline;
  &__icon {
    & .flex-icon {
      cursor: pointer;
    }
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "help"
    ]
  }
</lang-strings>
