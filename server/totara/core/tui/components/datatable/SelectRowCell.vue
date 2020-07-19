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
  <div
    class="tui-dataTableSelectRowCell"
    :class="[
      hidden && 'tui-dataTableSelectRowCell--hidden',
      valign && 'tui-dataTableSelectRowCell--valign-' + valign,
    ]"
    role="cell"
  >
    <Checkbox
      :large="largeCheckBox"
      :no-label-offset="noLabelOffset"
      :aria-label="$str('selectrow', 'totara_core') + ': ' + rowLabel"
      :checked="checked"
      :disabled="disabled || hidden"
      @change="$emit('change', $event)"
    />
  </div>
</template>

<script>
import Checkbox from 'totara_core/components/form/Checkbox';

export default {
  components: {
    Checkbox,
  },

  props: {
    checked: Boolean,
    disabled: Boolean,
    hidden: Boolean,
    largeCheckBox: Boolean,
    noLabelOffset: Boolean,
    valign: String,
    rowLabel: {
      type: String,
      required: true,
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "selectrow"
  ]
}
</lang-strings>
