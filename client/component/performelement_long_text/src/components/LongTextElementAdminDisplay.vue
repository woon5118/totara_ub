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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @module performelement_long_text
-->
<template>
  <ElementAdminDisplay
    :type="type"
    :title="title"
    :identifier="identifier"
    :error="error"
    :is-required="isRequired"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
    @display-read="$emit('display-read')"
  >
    <template v-slot:content>
      <Uniform>
        <LongTextElementParticipantForm disabled />
      </Uniform>
    </template>
  </ElementAdminDisplay>
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';
import LongTextElementParticipantForm from 'performelement_long_text/components/LongTextElementParticipantForm';
import Uniform from 'tui/components/uniform/Uniform';

export default {
  components: {
    ElementAdminDisplay,
    LongTextElementParticipantForm,
    Uniform,
  },
  props: {
    title: String,
    identifier: String,
    type: Object,
    data: Object,
    isRequired: Boolean,
    activityState: {
      type: Object,
      required: true,
    },
    error: String,
  },
};
</script>
