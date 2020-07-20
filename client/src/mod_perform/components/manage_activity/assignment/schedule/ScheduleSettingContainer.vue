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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performAssignmentScheduleSettingContainer">
    <h4
      :id="$id('settings-container-title')"
      class="tui-performAssignmentScheduleSettingContainer__title"
    >
      {{ title }}
    </h4>
    <div
      :aria-labelledby="$id('settings-container-title')"
      class="tui-performAssignmentScheduleSettingContainer__container"
    >
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    title: {
      required: true,
      type: String,
    },
  },
};
</script>
