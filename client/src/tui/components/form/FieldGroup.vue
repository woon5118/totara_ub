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

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @module totara_core
-->

<template>
  <div
    class="tui-formFieldGroup"
    role="group"
    :aria-labelledby="ariaLabelledby"
  >
    <slot
      :id="generatedId"
      :labelId="generatedLabelId"
      :label="label"
      :ariaDescribedby="ariaDescribedbyId"
    />
  </div>
</template>

<script>
export default {
  inject: { reformFieldContext: { default: null } },

  props: {
    ariaLabelledby: {
      type: String,
      required: true,
    },
    hidden: Boolean,
    id: String,
    label: String,
  },

  computed: {
    ariaDescribedbyId() {
      return this.helpmsg ? this.generatedId + 'helpDesc' : null;
    },
    ariaLabel() {
      return this.hidden ? this.label : null;
    },
    generatedId() {
      return this.id || this.$id();
    },
    generatedLabelId() {
      return this.$id('label');
    },
    labelId() {
      return (
        (this.reformFieldContext && this.reformFieldContext.getLabelId()) ||
        this.ariaLabelledby
      );
    },
  },
};
</script>
