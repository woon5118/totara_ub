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
  @module totara_core
-->

<template>
  <div class="tui-formRow">
    <div class="tui-formRow__desc">
      <Label
        v-if="label"
        :id="generatedLabelId"
        :for-id="generatedId"
        :legend="labelLegend"
        :hidden="hidden"
        :label="label"
        :required="required"
      />
      <HelpIcon
        v-if="helpmsg"
        :desc-id="ariaDescribedbyId"
        :helpmsg="helpmsg"
        :hidden="hidden"
      />
    </div>

    <FieldContextProvider :id="generatedId" :label-id="generatedLabelId">
      <div
        :class="{
          'tui-formRow__action': true,
          'tui-formRow__action--isStacked': isStacked,
        }"
      >
        <slot
          :id="generatedId"
          :labelId="generatedLabelId"
          :label="label"
          :ariaDescribedby="ariaDescribedbyId"
          :ariaLabel="ariaLabel"
        />
      </div>
    </FieldContextProvider>
  </div>
</template>

<script>
// Components
import HelpIcon from 'tui/components/form/HelpIcon';
import Label from 'tui/components/form/Label';
import FieldContextProvider from 'tui/components/reform/FieldContextProvider';

export default {
  components: {
    HelpIcon,
    Label,
    FieldContextProvider,
  },

  props: {
    labelLegend: Boolean,
    helpmsg: String,
    hidden: Boolean,
    id: String,
    label: String,
    required: Boolean,
    isStacked: Boolean,
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
  },
};
</script>
