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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package performelement_multi_choice_multi
-->
<template>
  <ElementAdminReadOnlyDisplay
    :type="type"
    :title="title"
    :identifier="identifier"
    :is-required="isRequired"
    :activity-state="activityState"
    @display="$emit('display')"
  >
    <template v-slot:content>
      <FormRow
        :label="
          $str('multi_select_options', 'performelement_multi_choice_multi')
        "
      >
        <div class="tui-multiChoiceSingleElementAdminReadOnlyDisplay__options">
          <div
            v-for="item in data.options"
            :key="item.name"
            class="tui-multiChoiceSingleElementAdminReadOnlyDisplay__options-item"
          >
            {{ item.value }}
          </div>
        </div>
      </FormRow>
    </template>
  </ElementAdminReadOnlyDisplay>
</template>

<script>
import ElementAdminReadOnlyDisplay from 'mod_perform/components/element/ElementAdminReadOnlyDisplay';
import FormRow from 'totara_core/components/form/FormRow';

export default {
  components: {
    FormRow,
    ElementAdminReadOnlyDisplay,
  },

  props: {
    title: String,
    identifier: String,
    type: Object,
    data: Object,
    isRequired: Boolean,
    error: String,
    activityState: {
      type: Object,
      required: true,
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_multi_choice_multi": [
    "multi_select_options"
  ]
  }
</lang-strings>
