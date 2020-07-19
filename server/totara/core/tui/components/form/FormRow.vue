<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-formRow">
    <div class="tui-formRow__desc">
      <Label
        v-if="label"
        :id="generatedLabelId"
        :for-id="generatedId"
        :legend="labelLegend"
        :hidden="hidden"
        :label="label"
        :required="required"
      />
      <HelpIcon
        v-if="helpmsg"
        :desc-id="ariaDescribedbyId"
        :helpmsg="helpmsg"
        :hidden="hidden"
      />
    </div>

    <FieldContextProvider :id="generatedId" :label-id="generatedLabelId">
      <div
        :class="{
          'tui-formRow__action': true,
          'tui-formRow__action--isStacked': isStacked,
        }"
      >
        <slot
          :id="generatedId"
          :labelId="generatedLabelId"
          :label="label"
          :ariaDescribedby="ariaDescribedbyId"
          :ariaLabel="ariaLabel"
        />
      </div>
    </FieldContextProvider>
  </div>
</template>

<script>
// Components
import HelpIcon from 'totara_core/components/form/HelpIcon';
import Label from 'totara_core/components/form/Label';
import FieldContextProvider from 'totara_core/components/reform/FieldContextProvider';

export default {
  components: {
    HelpIcon,
    Label,
    FieldContextProvider,
  },

  props: {
    labelLegend: Boolean,
    helpmsg: String,
    hidden: Boolean,
    id: String,
    label: String,
    required: Boolean,
    isStacked: Boolean,
  },

  computed: {
    ariaDescribedbyId() {
      return this.helpmsg ? this.generatedId + 'helpDesc' : null;
    },
    ariaLabel() {
      return this.hidden ? this.label : null;
    },
    generatedId() {
      return this.id || this.$id();
    },
    generatedLabelId() {
      return this.$id('label');
    },
  },
};
</script>
