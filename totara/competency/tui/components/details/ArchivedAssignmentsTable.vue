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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <Table :data="assignments">
    <template v-slot:header-row>
      <HeaderCell size="6">
        {{ $str('assignment', 'totara_competency') }}
      </HeaderCell>
      <HeaderCell size="3">
        {{ $str('assignment_date_archived', 'totara_competency') }}
      </HeaderCell>
      <HeaderCell size="3">
        {{ $str('proficient', 'totara_competency') }}
      </HeaderCell>
    </template>
    <template v-slot:row="{ row, expand }">
      <!-- Assignment name column -->
      <Cell size="6" :column-header="$str('assignment', 'totara_competency')">
        {{ row.name }}
      </Cell>

      <!-- Archived Date column -->
      <Cell
        size="3"
        :column-header="$str('assignment_date_archived', 'totara_competency')"
      >
        {{ row.archivedAt }}
      </Cell>

      <!-- Proficiency status column -->
      <Cell size="3" :column-header="$str('proficient', 'totara_competency')">
        <div class="tui-competencyDetailArchivedAssignments__achievement">
          {{ row.proficient }}
          <InfoIconButton
            v-if="row.legacy"
            :aria-label="$str('more_information', 'totara_competency')"
          >
            <h4 class="tui-competencyDetailArchivedAssignments__discontinued">
              {{
                $str(
                  'legacy_assignment_rating_discontinued',
                  'totara_competency'
                )
              }}
            </h4>
            {{
              $str('legacy_assignment_rating_description', 'totara_competency')
            }}
          </InfoIconButton>
        </div>
      </Cell>
    </template>
  </Table>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Cell,
    HeaderCell,
    InfoIconButton,
    Table,
  },
  props: {
    assignments: {
      type: Array,
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "assignment",
      "assignment_date_archived",
      "proficient",
      "legacy_assignment_rating_discontinued",
      "legacy_assignment_rating_description",
      "more_information"
    ]
  }
</lang-strings>
