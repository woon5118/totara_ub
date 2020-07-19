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

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @module mod_perform
-->

<template>
  <Collapsible
    v-model="expanded"
    class="tui-activityNotificationSettings"
    :label="data.name"
  >
    <template v-slot:collapsible-side-content>
      <div class="tui-activityNotificationSettings__toggle">
        <ToggleSwitch
          class="tui-activityNotificationSettings__active"
          text=""
          :aria-label="$str('toggle_notification', 'mod_perform', data.name)"
          :value="data.active"
          @input="$emit('toggleNotification', data, $event)"
        />
      </div>
    </template>
    <Form input-width="full">
      <FormRow
        :label="$str('recipients', 'mod_perform')"
        class="tui-activityNotificationsRecipients"
      >
        <RecipientsTable
          v-if="data.recipients.length"
          :data="data.recipients"
          :class-key="data.class_key"
          @toggle="$emit('toggleRecipient', data, $event)"
        />
        <div v-else>{{ $str('no_recipients', 'mod_perform') }}</div>
      </FormRow>
    </Form>
  </Collapsible>
</template>

<script>
import Collapsible from 'tui/components/collapsible/Collapsible';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';
import RecipientsTable from 'mod_perform/components/manage_activity/notification/RecipientsTable';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';

export default {
  components: {
    Collapsible,
    ToggleSwitch,
    RecipientsTable,
    Form,
    FormRow,
  },

  props: {
    data: {
      required: true,
      type: Object,
    },
    preview: Object,
  },

  data() {
    return {
      isSection: true,
      expanded: this.data.active,
    };
  },
};
</script>

<lang-strings>
{
  "mod_perform": [
    "no_recipients",
    "recipients",
    "toggle_notification"
  ]
}
</lang-strings>
