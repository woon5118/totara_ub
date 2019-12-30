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
  <a href="#" class="tui-formHelpIcon">
    <FlexIcon
      v-if="!hidden"
      icon="help"
      :alt="$str('help', 'moodle')"
      :title="helpmsg"
    />
    <div :id="descId" class="tui-formHelpIcon__desc">
      {{ helpmsg }}
    </div>
  </a>
</template>

<script>
// Components
import FlexIcon from 'totara_core/components/icons/FlexIcon';

export default {
  components: {
    FlexIcon: FlexIcon,
  },

  props: {
    descId: {
      required: true,
      type: String,
    },
    helpmsg: {
      required: true,
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
