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
  @package totara_samples
-->

<template>
  <div>
    <SamplesExample>
      <Basket :items="items" :bulk-actions="bulkActions">
        <template v-slot:status="{ empty }">
          <ButtonIcon
            v-if="viewingSelected && !empty"
            :styleclass="{ small: true }"
            text="Clear selection"
            @click="clear"
          >
            <ClearIcon />
          </ButtonIcon>
        </template>
        <template v-slot:actions="{ empty }">
          <Button
            v-if="!viewingSelected && !empty"
            :styleclass="{ transparent: true }"
            text="View selected"
            @click="viewSelected"
          />
          <Button
            v-if="viewingSelected && !empty"
            :styleclass="{ transparent: true }"
            text="Go back to all"
            @click="showAll"
          />
        </template>
      </Basket>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Items">
        <RadioGroup v-model="selectedItems" :horizontal="true">
          <Radio :value="0">None</Radio>
          <Radio :value="1">One</Radio>
          <Radio :value="10">Ten</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Bulk actions">
        <RadioGroup v-model="bulkActions" :horizontal="true">
          <Radio :value="actionOptions[0]">None</Radio>
          <Radio :value="actionOptions[1]">One</Radio>
          <Radio :value="actionOptions[2]">Three</Radio>
        </RadioGroup>
      </FormRow>
    </SamplesPropCtl>
  </div>
</template>

<script>
import Basket from 'totara_core/components/basket/Basket';
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import ClearIcon from 'totara_core/components/icons/common/Clear';
import FormRow from 'totara_core/components/form/FormRow';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Radio from 'totara_core/components/form/Radio';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Basket,
    Button,
    ButtonIcon,
    ClearIcon,
    FormRow,
    RadioGroup,
    Radio,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    const actionOptions = [
      [],
      [{ label: 'Activate', action: this.action }],
      [
        { label: 'Activate', action: this.action },
        { label: 'Archive', action: this.action },
        { label: 'Delete', action: this.action },
      ],
    ];
    return {
      actionOptions,
      items: ['hello'],
      bulkActions: actionOptions[2],
      viewingSelected: false,
    };
  },

  computed: {
    selectedItems: {
      get() {
        return this.items.length;
      },

      set(length) {
        // hacky way to generate an array of `length`
        this.items = Array.apply(null, Array(length)).map(() => 1);
      },
    },
  },

  methods: {
    action() {
      setTimeout(() => alert('action!'), 20);
    },

    viewSelected() {
      this.viewingSelected = true;
    },

    showAll() {
      this.viewingSelected = false;
    },

    clear() {
      this.items = [];
    },
  },
};
</script>
