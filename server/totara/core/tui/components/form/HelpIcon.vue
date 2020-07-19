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
  @package totara_core
-->

<template>
  <div class="tui-formHelpIcon">
    <Popover :position="position">
      <template v-slot:trigger>
        <a href="javascript:;" class="tui-formHelpIcon__icon">
          <Info
            v-if="!hidden"
            :alt="$str('help', 'moodle')"
            :title="iconLabel || helpmsg"
          />
        </a>
      </template>
      <slot v-if="$slots.default" />
      <template v-else>{{ helpmsg }}</template>
    </Popover>

    <div :id="descId" class="tui-formHelpIcon__desc">
      <slot v-if="$slots.default" />
      <template v-else>{{ helpmsg }}</template>
    </div>
  </div>
</template>

<script>
// Components
import Info from 'totara_core/components/icons/common/Info';
import Popover from 'totara_core/components/popover/Popover';

export default {
  components: {
    Info,
    Popover,
  },

  props: {
    position: {
      type: String,
      default: 'right',
    },
    descId: {
      required: true,
      type: String,
    },
    helpmsg: {
      type: String,
    },
    iconLabel: {
      type: String,
    },
    hidden: {
      type: Boolean,
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "help"
  ]
}
</lang-strings>
