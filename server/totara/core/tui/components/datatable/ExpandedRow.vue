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
  <div class="tui-dataTableExpandableRow" @keydown.esc="$emit('close')">
    <Arrow size="large" side="top" :distance="32" />
    <div class="tui-dataTableExpandableRow__placement">
      <div class="tui-dataTableExpandableRow__close">
        <CloseButton @click="$emit('close', $event.target)" />
      </div>

      <div class="tui-dataTableExpandableRow__content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
import Arrow from 'totara_core/components/decor/Arrow';
import CloseButton from 'totara_core/components/buttons/CloseIcon';

export default {
  components: {
    Arrow,
    CloseButton,
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "closebuttontitle"
  ]
}
</lang-strings>
