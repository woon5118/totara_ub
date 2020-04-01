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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_competency
-->

<template>
  <Table :data="competencies">
    <template v-slot:header-row>
      <HeaderCell size="10">
        {{ $str('header_competency', 'totara_competency') }}
      </HeaderCell>
      <HeaderCell size="2" align="center">
        {{ $str('proficient', 'totara_competency') }}
      </HeaderCell>
      <HeaderCell size="3">
        {{ $str('achievement_level', 'totara_competency') }}
      </HeaderCell>
    </template>
    <template v-slot:row="{ row }">
      <Cell
        size="10"
        :column-header="$str('header_competency', 'totara_competency')"
      >
        <a :href="competencyDetailsLink(row)">{{ row.competency.fullname }}</a>
      </Cell>

      <Cell
        size="2"
        align="center"
        :column-header="$str('proficient', 'totara_competency')"
      >
        <CheckIcon
          v-if="row.items[0].my_value && row.items[0].my_value.proficient"
          size="200"
          :alt="$str('yes')"
        />
        <span v-else>
          <span :aria-hidden="true">-</span>
          <span class="sr-only">{{ $str('no') }}</span>
        </span>
      </Cell>

      <Cell
        size="3"
        :column-header="$str('achievement_level', 'totara_competency')"
      >
        <MyRatingCell
          v-if="row.items[0].my_value"
          :value="row.items[0].my_value"
          :scales="scales"
        />
      </Cell>
    </template>
  </Table>
</template>

<script>
  import Table from 'totara_core/components/datatable/Table';
  import HeaderCell from 'totara_core/components/datatable/HeaderCell';
  import Cell from 'totara_core/components/datatable/Cell';
  import CheckIcon from 'totara_core/components/icons/common/CheckSuccess';
  import MyRatingCell from 'totara_competency/components/profile/MyRatingCell';

  export default {
    components: {
      Table,
      HeaderCell,
      Cell,
      CheckIcon,
      MyRatingCell,
    },

    props: {
      competencies: {
      required: true,
      type: Array,
    },
    baseUrl: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
    scales: {
      required: true,
      type: Array,
    },
  },

  methods: {
    competencyDetailsLink(row) {
      const params = { competency_id: row.competency.id };
      if (!this.isMine) {
        params.user_id = this.userId;
      }
      return this.$url(`${this.baseUrl}/details/`, params);
    },
  },
};
</script>

<lang-strings>
{
  "moodle": ["yes", "no"],
  "totara_competency": [
    "header_competency",
    "achievement_level",
    "proficient"
  ]
}
</lang-strings>
