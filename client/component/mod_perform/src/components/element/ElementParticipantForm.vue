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
  <div class="tui-performElementResponse">
    <template v-if="activeSectionIsClosed || fromPrint">
      <div class="tui-performElementResponse__section">
        <div class="tui-performElementResponse__label">
          {{ $str('your_response', 'mod_perform') }}
        </div>
        <slot name="content" />
      </div>
    </template>
    <template v-else>
      <div v-if="error">{{ error }}</div>
      <FormRow
        v-slot="{ labelId }"
        :label="$str('your_response', 'mod_perform')"
        :accessible-label="accessibleLabel"
        :required="required"
        :optional="optional"
        :aria-describedby="ariaDescribedby"
      >
        <slot name="content" :labelId="labelId" />
      </FormRow>
    </template>
  </div>
</template>

<script>
import FormRow from 'tui/components/form/FormRow';

export default {
  components: { FormRow },
  props: {
    name: String,
    type: Object,
    error: String,
    accessibleLabel: String,
    required: Boolean,
    optional: Boolean,
    ariaDescribedby: String,
    activeSectionIsClosed: Boolean,
    fromPrint: Boolean,
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "your_response"
    ]
  }
</lang-strings>
<style lang="scss">
.tui-performElementResponse {
  &__section {
    display: flex;
  }

  @media (max-width: $tui-screen-sm) {
    &__section {
      display: block;
    }
  }

  &__label {
    @include tui-font-heading-label();
    flex-basis: 22rem;
  }
}
</style>
