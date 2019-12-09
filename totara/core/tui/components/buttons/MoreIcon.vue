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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <ButtonIcon
    :aria-label="ariaLabel || $str('more', 'moodle')"
    :styleclass="{ transparent: true, transparentNoPadding: noPadding }"
    @click.prevent="$emit('click', $event)"
  >
    <MoreIcon :size="size" />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import MoreIcon from 'totara_core/components/icons/common/More';

export default {
  components: {
    ButtonIcon,
    MoreIcon,
  },

  props: {
    ariaLabel: String,
    noPadding: Boolean,
    size: {
      type: Number,
      default: 200,
    },
  },
};
</script>

<lang-strings>
{
  "moodle": ["more"]
}
</lang-strings>
