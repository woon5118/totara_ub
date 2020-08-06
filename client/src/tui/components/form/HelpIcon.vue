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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module tui
-->

<template>
  <div class="tui-formHelpIcon">
    <Popover position="top" :title="title" :triggers="['click']">
      <template v-slot:trigger>
        <ButtonIcon
          class="tui-formHelpIcon__icon"
          :aria-label="$str('help', 'moodle')"
          :title="iconLabel || helpmsg"
          :styleclass="{ transparent: true }"
        >
          <Info v-if="!hidden" />
        </ButtonIcon>
      </template>
      <slot v-if="$slots.default" />
      <template v-else>{{ helpmsg }}</template>
    </Popover>

    <div :id="descId" class="tui-formHelpIcon__desc">
      <slot v-if="$slots.default" />
      <template v-else>{{ helpmsg }}</template>
    </div>
  </div>
</template>

<script>
// Components
import Info from 'tui/components/icons/common/Info';
import Popover from 'tui/components/popover/Popover';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';

export default {
  components: {
    Info,
    Popover,
    ButtonIcon,
  },

  props: {
    descId: {
      required: true,
      type: String,
    },
    helpmsg: {
      type: String,
    },
    iconLabel: {
      type: String,
    },
    hidden: {
      type: Boolean,
    },
    title: {
      type: String,
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "help"
  ]
}
</lang-strings>
