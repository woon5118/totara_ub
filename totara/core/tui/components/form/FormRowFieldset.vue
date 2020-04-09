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
  @package totara_core
-->

<template>
  <fieldset class="tui-formRowFieldset">
    <FormRow label-legend v-bind="$attrs">
      <template v-if="$scopedSlots.default" v-slot="slotProps">
        <slot v-bind="slotProps" />
      </template>
    </FormRow>
  </fieldset>
</template>

<script>
// Components
import FormRow from 'totara_core/components/form/FormRow';

export default {
  components: {
    FormRow,
  },

  inheritAttrs: false,
};
</script>
