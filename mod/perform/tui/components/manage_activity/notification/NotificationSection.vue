<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @package mod_perform
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
import Collapsible from 'totara_core/components/collapsible/Collapsible';
import ToggleSwitch from 'totara_core/components/toggle/ToggleSwitch';
import RecipientsTable from 'mod_perform/components/manage_activity/notification/RecipientsTable';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';

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
