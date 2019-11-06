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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <Table
    :data="scale.competencies"
    :expandable-rows="false"
    class="tui-pathwayManual-scaleTable"
  >
    <template v-slot:header-row>
      <HeaderCell size="4">
        <strong>{{ $str('competency', 'totara_hierarchy') }}</strong>
      </HeaderCell>
      <HeaderCell size="1">
        <strong>{{ $str('last_rating_given', 'pathway_manual') }}</strong>
      </HeaderCell>
      <HeaderCell size="2" class="tui-pathwayManual-scaleTable__block">
        <div class="tui-pathwayManual-scaleTable__block">
          <strong>{{ $str('rate_competency', 'pathway_manual') }}</strong>
          <div
            class="tui-pathwayManual-scaleTable__help"
            @mouseover="showScaleTooltip = true"
            @mouseleave="showScaleTooltip = false"
          >
            <FlexIcon icon="info" size="200" />
            <ScaleTooltip
              :scale="scale"
              :display="showScaleTooltip"
              :show-descriptions="true"
            />
          </div>
        </div>
      </HeaderCell>
    </template>
    <template v-slot:row="{ row }">
      <Cell size="4">
        {{ row.competency.display_name }}
      </Cell>
      <Cell size="1" />
      <Cell size="2" />
    </template>
  </Table>
</template>

<script>
import Cell from 'totara_core/presentation/datatable/Cell';
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import HeaderCell from 'totara_core/presentation/datatable/HeaderCell';
import ScaleTooltip from 'totara_competency/presentation/ScaleTooltip';
import Table from 'totara_core/presentation/datatable/Table';

export default {
  components: { Cell, FlexIcon, HeaderCell, ScaleTooltip, Table },

  props: {
    scale: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      showScaleTooltip: false,
    };
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-scaleTable {
  &:not(:last-child) {
    margin-bottom: var(--tui-gap-7);
  }
  &__block {
    display: block;
  }
  &__help {
    display: inline;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "last_rating_given",
      "rate_competency"
    ],
    "totara_hierarchy": [
      "competency"
    ]
  }
</lang-strings>
