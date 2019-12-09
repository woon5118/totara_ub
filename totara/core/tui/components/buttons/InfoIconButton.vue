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
  <Popover :triggers="['click']">
    <template v-slot:trigger>
      <ButtonIcon
        :aria-label="ariaLabel"
        :disabled="disabled"
        class="tui-InfoIconButton"
        :styleclass="{
          circle: true,
          primary: true,
          small: true,
          transparent: true,
        }"
      >
        <InfoIcon />
      </ButtonIcon>
    </template>
    <slot />
  </Popover>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import InfoIcon from 'totara_core/components/icons/common/Info';
import Popover from 'totara_core/components/popover/Popover';

export default {
  components: {
    ButtonIcon,
    InfoIcon,
    Popover,
  },

  props: {
    ariaLabel: {
      required: true,
      type: String,
    },
    disabled: Boolean,
  },
};
</script>
