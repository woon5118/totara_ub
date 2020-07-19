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
  <div class="tui-testDropdown">
    <SamplesExample>
      <div class="tui-testDropdown__wrapper">
        <Dropdown
          :separator="values.separator"
          :disabled="values.disabled"
          :position="values.position"
        >
          <template v-slot:trigger="{ toggle, isOpen }">
            <Button
              :aria-expanded="isOpen ? 'true' : 'false'"
              aria-label="Dropdown"
              :disabled="values.disabled"
              :caret="true"
              text="Dropdown"
              @click="toggle"
            >
              <AddIcon />
            </Button>
          </template>
          <DropdownItem @click="doThing">Action</DropdownItem>
          <DropdownItem disabled @click="doThing">Another action</DropdownItem>
          <DropdownItem @click="doThing">
            This is an extra long dropdown item
          </DropdownItem>
          <DropdownItem href="https://www.google.com/"
            >External link</DropdownItem
          >
        </Dropdown>

        <Dropdown
          :separator="values.separator"
          :disabled="values.disabled"
          :position="values.position"
        >
          <template v-slot:trigger="{ toggle, isOpen }">
            <ButtonIcon
              :aria-expanded="isOpen ? 'true' : 'false'"
              aria-label="Add"
              :disabled="values.disabled"
              :caret="true"
              @click="toggle"
            >
              <AddIcon />
            </ButtonIcon>
          </template>
          <DropdownItem @click="doThing">Action</DropdownItem>
          <DropdownItem @click="doThing">Another action</DropdownItem>
        </Dropdown>
      </div>
    </SamplesExample>

    <SamplesPropCtl>
      <Uniform :initial-values="values" @change="v => (values = v)">
        <FormRow label="Separator">
          <FormRadioGroup name="separator" :horizontal="true">
            <Radio :value="true">True</Radio>
            <Radio :value="false">False</Radio>
          </FormRadioGroup>
        </FormRow>

        <FormRow label="Disabled">
          <FormRadioGroup name="disabled" :horizontal="true">
            <Radio :value="true">True</Radio>
            <Radio :value="false">False</Radio>
          </FormRadioGroup>
        </FormRow>

        <FormRow label="Position">
          <FormRadioGroup name="position" :horizontal="true">
            <Radio :value="undefined">Unspecified</Radio>
            <Radio value="top-left">top-left</Radio>
            <Radio value="top-right">top-right</Radio>
            <Radio value="bottom-left">bottom-left</Radio>
            <Radio value="bottom-right">bottom-right</Radio>
          </FormRadioGroup>
        </FormRow>
      </Uniform>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import {
  Uniform,
  FormRow,
  FormRadioGroup,
} from 'totara_core/components/uniform';
import Radio from 'totara_core/components/form/Radio';
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Button,
    Dropdown,
    DropdownItem,
    Uniform,
    FormRow,
    FormRadioGroup,
    Radio,
    AddIcon,
    ButtonIcon,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      values: {
        position: undefined,
        separator: false,
        disabled: false,
      },

      codeTemplate: `<Dropdown
  :separator="separator"
  :disabled="disabled"
  :position="position"
>
  <template v-slot:trigger="{ toggle, isOpen }">
    <Button
      :aria-expanded="isOpen ? 'true' : 'false'"
      aria-label="Dropdown"
      :disabled="disabled"
      :caret="true"
      text="Dropdown"
      @click="toggle"
    >
      <AddIcon />
    </Button>
  </template>
  <DropdownItem @click="doThing">Action</DropdownItem>
</Dropdown>`,
    };
  },

  methods: {
    doThing() {
      console.log('clicked');
    },
  },
};
</script>

<style lang="scss">
.tui-testDropdown__wrapper {
  display: flex;

  & > * + * {
    margin-left: var(--tui-gap-1);
  }
}
</style>
