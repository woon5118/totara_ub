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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module mod_perform
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
    <div v-if="title" class="tui-performElementEditDisplay__title">
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
import EditIcon from 'tui/components/buttons/EditIcon';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import SettingsIcon from 'tui/components/icons/common/Settings';
import Popover from 'tui/components/popover/Popover';
import ReportingIdentifierIcon from 'mod_perform/components/icons/ReportingIdentifier';
import DeleteIcon from 'tui/components/buttons/DeleteIcon';
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
