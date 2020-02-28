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

  @author Murali Nair <murali.nair@totaralearning.com>
  @package mod_perform
-->

<template>
  <Modal :aria-labelledby="$id('title')">
    <ModalContent
      :title="$str('perform:user_group_assignment:add:cohort', 'mod_perform')"
      :title-id="$id('title')"
      :close-button="false"
    >
      <div>
        <SelectTable
          v-model="selection"
          :color-odd-rows="true"
          :data="pages[page]"
          :expandable-rows="true"
          :select-all-enabled="selectAllMode"
          :select-entire-enabled="allowSelectEntire"
          :entire-selected="entireSelected"
          @select-entire="handleSelectEntire"
        >
          <template v-slot:header-row>
            <HeaderCell size="16">{{
              $str(
                'perform:user_group_assignment:group:cohort:name',
                'mod_perform'
              )
            }}</HeaderCell>
          </template>
          <template v-slot:row="{ row }">
            <Cell size="16" column-header="Title">
              <a href="#">{{ row.name }}</a>
            </Cell>
          </template>
        </SelectTable>

        <input v-model="page" type="range" />
      </div>

      <FormRowActionButtons @cancel="formCancel" @submit.prevent="formSubmit" />
    </ModalContent>
  </Modal>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import CohortQuery from '../../../webapi/ajax/user_grouping_cohorts.graphql';
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import SelectTable from 'totara_core/components/datatable/SelectTable';
import { groupBy } from 'totara_core/util';

export default {
  components: {
    Cell,
    FormRowActionButtons,
    HeaderCell,
    Modal,
    ModalContent,
    SelectTable,
  },

  props: {
    assigned: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      cohorts: [],
      entireSelected: false,
      allowSelectEntire: true,
      selectAllMode: true,
      page: 0,
      selection: [],
    };
  },

  computed: {
    pages() {
      const filtered = this.cohorts.filter(
        x => this.assigned.indexOf(x.id) == -1
      );
      return groupBy(filtered, x => Math.floor(filtered.indexOf(x) / 10));
    },
  },

  watch: {
    selectAllMode() {
      this.entireSelected = false;
    },
  },

  methods: {
    handleSelectEntire(value) {
      this.entireSelected = value;
    },

    formCancel() {
      this.$emit('request-close');
    },

    formSubmit() {
      this.$emit('request-close', { selected: this.selection });
    },
  },

  apollo: {
    cohorts: {
      query: CohortQuery,
      update: data => data.mod_perform_user_grouping_cohorts,
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "perform:user_group_assignment:add:cohort",
      "perform:user_group_assignment:group:cohort:name"
    ]
  }
</lang-strings>
