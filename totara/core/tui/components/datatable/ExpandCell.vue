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
    class="tui-dataTableExpandCell"
    :class="{ 'tui-dataTableExpandCell--header': header }"
    role="cell"
  >
    <template v-if="!header || empty">
      <ButtonIcon
        :aria-label="
          $str(
            expandState ? 'a11y_collapse_row' : 'a11y_expand_row',
            'totara_core'
          )
        "
        :styleclass="{
          transparent: true,
        }"
        :text="$str('a11y_expand_row', 'totara_core')"
        @click="$emit('click', $event)"
      >
        <CollapseIcon v-if="expandState" size="100" />
        <ExpandIcon v-else size="100" />
      </ButtonIcon>
    </template>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import CollapseIcon from 'totara_core/components/icons/common/Collapse';
import ExpandIcon from 'totara_core/components/icons/common/Expand';

export default {
  components: {
    ButtonIcon,
    CollapseIcon,
    ExpandIcon,
  },

  props: {
    empty: Boolean,
    expandState: Boolean,
    header: Boolean,
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "a11y_collapse_row",
    "a11y_expand_row"
  ]
}
</lang-strings>
