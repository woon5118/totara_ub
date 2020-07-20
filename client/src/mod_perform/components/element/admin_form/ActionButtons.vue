<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
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
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Button from 'tui/components/buttons/Button';

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
