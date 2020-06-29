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

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-performElementEditDisplay">
    <div v-if="error">{{ error }}</div>

    <div class="tui-performElementEditDisplay__action">
      <Popover
        v-if="identifier && !isActive"
        class="tui-performElementEditDisplay__reportingId"
      >
        <h2 class="tui-performElementEditDisplay__reportingId-header">
          {{ $str('reporting_identifier', 'mod_perform') }}
        </h2>
        <div class="tui-performElementEditDisplay__reportingId-content">
          {{ identifier }}
        </div>
        <template v-slot:trigger>
          <ButtonIcon
            :aria-label="$str('reporting_identifier', 'mod_perform')"
            :styleclass="{ transparentNoPadding: true }"
          >
            <template>
              <ReportingIdentifierIcon
                :alt="$str('reporting_identifier', 'mod_perform')"
                :title="$str('reporting_identifier', 'mod_perform')"
                size="200"
              />
            </template>
          </ButtonIcon>
        </template>
      </Popover>
      <EditIcon
        v-if="!isActive"
        :aria-label="$str('edit_element', 'mod_perform')"
        @click="edit"
      />
      <DeleteIcon
        v-if="!isActive"
        :aria-label="$str('delete_element', 'mod_perform')"
        @click="remove"
      />
      <ButtonIcon
        v-if="isActive"
        :aria-label="$str('setting_element', 'mod_perform')"
        :styleclass="{ transparentNoPadding: true }"
        class="tui-performElementEditDisplay__action-settings"
        @click.prevent="displayRead()"
      >
        <SettingsIcon size="200" />
      </ButtonIcon>
    </div>
    <div class="tui-performElementEditDisplay__title">
      {{ title }}

      <span
        v-if="isRequired"
        class="tui-performElementEditDisplay__title-response-required"
        >*</span
      >
    </div>
    <div class="tui-performElementEditDisplay__content">
      <slot name="content" />
    </div>
  </div>
</template>

<script>
import EditIcon from 'totara_core/components/buttons/EditIcon';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import SettingsIcon from 'totara_core/components/icons/common/Settings';
import Popover from 'totara_core/components/popover/Popover';
import ReportingIdentifierIcon from 'mod_perform/components/icons/ReportingIdentifier';
import DeleteIcon from 'totara_core/components/buttons/DeleteIcon';
export default {
  components: {
    ButtonIcon,
    DeleteIcon,
    EditIcon,
    SettingsIcon,
    Popover,
    ReportingIdentifierIcon,
  },

  props: {
    title: {
      type: String,
      required: true,
    },
    activityState: {
      type: Object,
      required: true,
    },
    type: {
      type: Object,
      required: true,
    },
    error: {
      type: String,
    },
    isRequired: {
      type: Boolean,
    },
    identifier: String,
  },

  computed: {
    isActive() {
      return this.activityState.name === 'ACTIVE';
    },
  },

  methods: {
    edit() {
      this.$emit('edit');
    },
    remove() {
      this.$emit('remove');
    },
    displayRead() {
      this.$emit('display-read');
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "delete_element",
      "edit_element",
      "reporting_identifier",
      "setting_element",
      "section_element_tag_required",
      "section_element_tag_optional"
    ]
  }
</lang-strings>
