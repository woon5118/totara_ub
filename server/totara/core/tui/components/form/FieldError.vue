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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div v-if="error" class="tui-formFieldError">
    <div class="tui-formFieldError__inner">
      <ErrorIcon custom-class="tui-formFieldError__icon" />
      {{ error }}

      <ButtonIcon
        v-if="dismissable"
        :aria-label="$str('closebuttontitle', 'moodle')"
        class="tui-formFieldError__dismiss"
        :styleclass="{
          small: true,
          transparent: true,
        }"
        @click="$emit('dismiss')"
      >
        <CloseIcon custom-class="tui-formFieldError__dismiss-icon" />
      </ButtonIcon>
    </div>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import CloseIcon from 'totara_core/components/icons/common/Close';
import ErrorIcon from 'totara_core/components/icons/common/Error';

export default {
  components: {
    ButtonIcon,
    CloseIcon,
    ErrorIcon,
  },

  props: {
    error: String,
    dismissable: Boolean,
  },
};
</script>

<lang-strings>
{
  "moodle": ["closebuttontitle"]
}
</lang-strings>
