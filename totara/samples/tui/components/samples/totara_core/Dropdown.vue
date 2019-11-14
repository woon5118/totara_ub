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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_samples
-->

<template>
  <div class="tui-dropdownWrapper">
    <Dropdown :disabled="true">
      <template v-slot:trigger>
        <ButtonIcon
          :aria-label="'trigger'"
          :disabled="true"
          :styleclass="{ square: true }"
        >
          <AddIcon v-if="universe % 2 === 0" />
          <DeleteIcon v-else />
        </ButtonIcon>
      </template>
    </Dropdown>
    <Dropdown
      :separator="false"
      position="bottom-left"
      aria-label="firstLabel"
      aria-labelledby="and first labelledby"
    >
      <template v-slot:trigger="{ toggle }">
        <ButtonIcon
          :aria-label="'trigger'"
          :disabled="false"
          :styleclass="{ square: true }"
          @click.prevent="toggle"
        >
          <AddIcon v-if="universe % 3 === 0" />
          <DeleteIcon v-else />
        </ButtonIcon>
      </template>
      <DropdownItem href="https://google.com" @click="changeTheUniverse">
        Google
      </DropdownItem>
      <DropdownItem @click="changeTheUniverse">Another action</DropdownItem>
      <DropdownItem :disabled="disabled" @click="changeTheUniverse(disabled)">
        Disabled item
      </DropdownItem>
    </Dropdown>
    <Dropdown>
      <template v-slot:trigger="{ toggle }">
        <ButtonIcon
          :aria-label="'trigger'"
          :disabled="false"
          :styleclass="{ square: true }"
          @click.prevent="toggle"
        >
          <AddIcon v-if="universe % 5 === 0" />
          <DeleteIcon v-else />
        </ButtonIcon>
      </template>
      <DropdownItem @click="changeTheUniverse">
        Action
      </DropdownItem>
      <DropdownItem @click="changeTheUniverse">
        Another action
      </DropdownItem>
      <DropdownItem :disabled="disabled" @click="changeTheUniverse(disabled)">
        Truncate the long context as default
      </DropdownItem>
    </Dropdown>
    <Dropdown position="bottom-left" hoverable>
      <template v-slot:trigger="{ toggle }">
        <ButtonIcon
          :aria-label="'trigger'"
          :disabled="false"
          :styleclass="{ square: true }"
          @click.prevent="toggle"
        >
          <AddIcon v-if="universe > 5" />
          <DeleteIcon v-else />
        </ButtonIcon>
      </template>
      <DropdownItem @click="changeTheUniverse">
        It's a long context here. because it tries to explain the universe to
        you
      </DropdownItem>
      <DropdownItem @click="changeTheUniverse">
        Another action
      </DropdownItem>
      <DropdownItem :disabled="disabled" @click="changeTheUniverse(disabled)">
        Something else
      </DropdownItem>
    </Dropdown>
  </div>
</template>

<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/icons/common/Delete';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    DeleteIcon,
    Dropdown,
    DropdownItem,
  },

  data() {
    return {
      universe: 0,
      disabled: true,
    };
  },

  methods: {
    changeTheUniverse(disabled) {
      if (disabled) return;
      this.universe = parseInt(Math.random() * 10, 10);
    },
  },
};
</script>

<style lang="scss">
.tui-dropdownWrapper {
  display: inline-flex;
  & > * + * {
    margin-left: var(--tui-gap-1);
  }
}
</style>
