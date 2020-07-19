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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module tui
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
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import CloseIcon from 'tui/components/icons/common/Close';
import ErrorIcon from 'tui/components/icons/common/Error';

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
