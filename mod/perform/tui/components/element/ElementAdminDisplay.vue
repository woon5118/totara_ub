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
    <div class="tui-performElementEditDisplay__inner">
      <div class="tui-performElementEditDisplay__inner-header">
        <div class="tui-performElementEditDisplay__title">
          {{ title }}
        </div>
        <div class="tui-performElementEditDisplay__info">
          <Popover
            v-if="identifier"
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
          <div class="tui-performElementEditDisplay__actions">
            <EditIcon
              :aria-label="$str('edit_element', 'mod_perform')"
              @click="edit"
            />
            <DeleteIcon
              :aria-label="$str('delete_element', 'mod_perform')"
              @click="remove"
            />
          </div>
          <Lozenge
            v-show="isRequired"
            :text="$str('section_element_tag_required', 'mod_perform')"
          />
          <Lozenge
            v-show="!isRequired"
            :text="$str('section_element_tag_optional', 'mod_perform')"
            type="warning"
          />
        </div>
      </div>
      <div class="tui-performElementEditDisplay__inner-content">
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/buttons/DeleteIcon';
import EditIcon from 'totara_core/components/buttons/EditIcon';
import Lozenge from 'totara_core/components/lozenge/Lozenge';
import Popover from 'totara_core/components/popover/Popover';
import ReportingIdentifierIcon from 'mod_perform/components/icons/ReportingIdentifier';

export default {
  components: {
    ButtonIcon,
    DeleteIcon,
    EditIcon,
    Lozenge,
    Popover,
    ReportingIdentifierIcon,
  },

  props: {
    title: String,
    identifier: String,
    isRequired: Boolean,
    type: Object,
    error: String,
  },

  methods: {
    edit() {
      this.$emit('edit');
    },
    remove() {
      this.$emit('remove');
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
      "section_element_tag_required",
      "section_element_tag_optional"
    ]
  }
</lang-strings>
