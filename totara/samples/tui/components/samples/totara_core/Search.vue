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
  @package totara_samples
-->

<template>
  <div style="width: 500px;">
    <h2>Standalone</h2>
    <SearchBox
      v-model="searchBox"
      aria-label="Search for item"
      :disabled="disabled"
      placeholder="Searching"
      @submit="searchSubmit"
    />
    <br />
    <h2>In form</h2>
    <Form :class="'tui-testForm'">
      <FormRow v-slot="{ id, label }" :label="'Search data'">
        <SearchBox
          :id="id"
          v-model="searchBoxRow"
          :drop-label="true"
          :disabled="disabled"
          :placeholder="'Search here'"
          @input="searchInput"
          @submit="searchSubmit"
        />
      </FormRow>
    </Form>
    <br />
    <ButtonGroup>
      <Button text="disable" @click="disableFormInputs" />
      <Button text="enable" @click="enableFormInputs" />
    </ButtonGroup>
  </div>
</template>

<script>
// Components
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import SearchBox from 'totara_core/components/form/SearchBox';

export default {
  components: {
    Button,
    ButtonGroup,
    Form,
    FormRow,
    SearchBox,
  },

  data() {
    return {
      disabled: false,
      searchBox: '',
      searchBoxRow: '',
      searchFilter: '',
      searchFilterRow: '',
    };
  },

  methods: {
    /**
     * Disable input fields
     *
     */
    disableFormInputs() {
      this.disabled = true;
    },

    /**
     * Enable input fields
     *
     */
    enableFormInputs() {
      this.disabled = false;
    },

    /**
     * Search input change
     *
     */
    searchInput() {
      console.log(this.searchBoxRow);
    },

    /**
     * Triggered search submit
     *
     */
    searchSubmit() {
      this.disableFormInputs();
    },
  },
};
</script>
