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
  <div
    class="tui-dataTableSelectVisibleRowsCell"
    :class="{ 'tui-dataTableSelectVisibleRowsCell--hidden': hidden }"
    role="cell"
  >
    <Checkbox
      :large="largeCheckBox"
      :no-label-offset="noLabelOffset"
      :checked="checked"
      :aria-label="$str('selectallrows', 'totara_core')"
      :disabled="disabled || hidden"
      @change="$emit('change', $event)"
    />
    <span class="tui-dataTableCell__label">
      {{ $str('selectallrows', 'totara_core') }}
    </span>
  </div>
</template>

<script>
import Checkbox from 'tui/components/form/Checkbox';

export default {
  components: {
    Checkbox,
  },

  props: {
    checked: Boolean,
    disabled: Boolean,
    hidden: Boolean,
    largeCheckBox: Boolean,
    noLabelOffset: Boolean,
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "selectallrows"
  ]
}
</lang-strings>
