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
  <ul class="tui-activityNotificationsRecipientsTable">
    <li
      v-for="e in data"
      :key="e.relationship_id"
      class="tui-activityNotificationsRecipientsTable__row"
    >
      <ToggleSwitch
        :text="e.name"
        :value="e.active"
        :toggle-first="true"
        @input="onToggle(e, $event)"
      />
    </li>
  </ul>
</template>

<script>
import ToggleSwitch from 'totara_core/components/toggle/ToggleSwitch';

export default {
  components: {
    ToggleSwitch,
  },

  props: {
    data: {
      type: Array,
      required: true,
    },
    classKey: {
      type: String,
      required: true,
    },
  },

  methods: {
    onToggle(row, val) {
      this.$emit('toggle', {
        id: row.relationship_id,
        active: val,
      });
    },
  },
};
</script>
