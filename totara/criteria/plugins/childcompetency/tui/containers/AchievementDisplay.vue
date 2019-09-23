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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @package criteria_childcompetency
-->

<template>
  <div>
    <Preloader :display="$apollo.loading" />
    <Table :data="achievements.items" :expandable-rows="true">
      <template v-slot:HeaderRow="">
        <HeaderCell size="14">
          <h4>{{ $str('competencies', 'criteria_childcompetency') }}</h4>
        </HeaderCell>
        <HeaderCell size="2">
          <div>
            {{ achievedCompetencies }} / {{ achievements.items.length }}
          </div>
          <div v-if="achievements.aggregation_type === 2">
            {{
              $str(
                'required_only',
                'criteria_childcompetency',
                achievements.required_items
              )
            }}
          </div>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row, expand }">
        <Cell size="1" style="text-align: center;">
          <CheckSuccess v-if="row.value && row.value.proficient" size="300" />
          <CheckSuccess v-else size="300" custom-class="ft-state-disabled" />
        </Cell>

        <Cell size="13">
          <a href="#" @click.prevent="expand()">{{
            row.competency.fullname
          }}</a>
        </Cell>
        <Cell size="2">
          <span v-if="row.value" v-text="row.value.name" />
        </Cell>
      </template>

      <template v-slot:expandContent="{ row }">
        <h4>{{ row.competency.fullname }}</h4>
        <p v-html="row.competency.description" />
      </template>
    </Table>
  </div>
</template>

<script>
import AchievementsQuery from '../../webapi/ajax/achievements.graphql';
import Preloader from 'totara_competency/presentation/Preloader';
import HeaderCell from 'totara_core/presentation/datatable/HeaderCell';
import Cell from 'totara_core/presentation/datatable/Cell';
import CheckSuccess from 'totara_core/presentation/icons/common/CheckSuccess';
import Table from 'totara_core/presentation/datatable/Table';

export default {
  components: { CheckSuccess, Cell, HeaderCell, Preloader, Table },

  props: {
    instanceId: {
      required: true,
      type: Number,
    },
    userId: {
      required: true,
      type: Number,
    },
    assignmentId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      achievements: {
        items: [],
      },
    };
  },
  computed: {
    achievedCompetencies() {
      return this.achievements.items.reduce((total, current) => {
        if (current.value) {
          if (current.value.proficient) {
            total += 1;
          }
        }

        return total;
      }, 0);
    },
  },

  apollo: {
    achievements: {
      query: AchievementsQuery,
      context: { batch: true },
      variables() {
        return {
          instance_id: this.instanceId,
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ criteria_childcompetency_achievements: achievements }) {
        return achievements;
      },
    },
  },

  methods: {},
};
</script>
<lang-strings>
  {
    "criteria_childcompetency": [
      "competencies",
      "required_only"
    ]
  }

</lang-strings>
