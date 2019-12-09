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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <ButtonIcon
    :aria-label="ariaLabel || $str('closebuttontitle', 'moodle')"
    :styleclass="{ transparent: true }"
    @click.prevent="$emit('click', $event)"
  >
    <CloseIcon :size="size" />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import CloseIcon from 'totara_core/components/icons/common/Close';

export default {
  components: {
    ButtonIcon,
    CloseIcon,
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
  "moodle": ["closebuttontitle"]
}
</lang-strings>
