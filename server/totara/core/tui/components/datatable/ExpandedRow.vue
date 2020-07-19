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
  <div class="tui-dataTableExpandableRow" @keydown.esc="$emit('close')">
    <Arrow size="large" side="top" :distance="32" />
    <div class="tui-dataTableExpandableRow__placement">
      <div class="tui-dataTableExpandableRow__close">
        <CloseButton @click="$emit('close', $event.target)" />
      </div>

      <div class="tui-dataTableExpandableRow__content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
import Arrow from 'totara_core/components/decor/Arrow';
import CloseButton from 'totara_core/components/buttons/CloseIcon';

export default {
  components: {
    Arrow,
    CloseButton,
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "closebuttontitle"
  ]
}
</lang-strings>
