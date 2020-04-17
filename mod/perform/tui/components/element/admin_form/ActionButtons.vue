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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-elementAdminFormActionButtons">
    <ButtonGroup>
      <Button
        :styleclass="{ primary: true, small: true }"
        :text="$str('button_done', 'mod_perform')"
        :disabled="submitting"
        class="tui-elementAdminFormActionButtons__done"
        type="submit"
        @click="$emit('submit', $event)"
      />
      <Button
        :text="$str('button_cancel', 'mod_perform')"
        :styleclass="{ small: true }"
        class="tui-elementAdminFormActionButtons__cancel"
        @click="$emit('cancel')"
      />
    </ButtonGroup>
  </div>
</template>

<script>
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Button from 'totara_core/components/buttons/Button';

export default {
  components: {
    ButtonGroup,
    Button,
  },
  props: {
    submitting: Boolean,
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "button_done",
      "button_cancel"
    ]
  }
</lang-strings>
