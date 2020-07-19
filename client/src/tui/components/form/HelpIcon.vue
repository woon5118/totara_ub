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
  <div class="tui-formHelpIcon">
    <Popover :position="position">
      <template v-slot:trigger>
        <a href="javascript:;" class="tui-formHelpIcon__icon">
          <Info
            v-if="!hidden"
            :alt="$str('help', 'moodle')"
            :title="iconLabel || helpmsg"
          />
        </a>
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

export default {
  components: {
    Info,
    Popover,
  },

  props: {
    position: {
      type: String,
      default: 'right',
    },
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
