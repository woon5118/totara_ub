<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <Popover :triggers="['click']">
    <template v-slot:trigger="{ isOpen }">
      <ButtonIcon
        :aria-expanded="isOpen.toString()"
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
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import InfoIcon from 'tui/components/icons/common/Info';
import Popover from 'tui/components/popover/Popover';

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
