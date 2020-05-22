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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package totara_core
-->

<template>
  <ButtonIcon
    :aria-label="ariaLabel || $str('edit', 'moodle')"
    :styleclass="{ transparent: true }"
    @click.prevent="$emit('click', $event)"
  >
    <EditIcon :size="size" />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import EditIcon from 'totara_core/components/icons/common/Edit';

export default {
  components: {
    ButtonIcon,
    EditIcon,
  },

  props: {
    ariaLabel: String,
    size: {
      type: Number,
      default: 200,
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "edit"
  ]
}
</lang-strings>
