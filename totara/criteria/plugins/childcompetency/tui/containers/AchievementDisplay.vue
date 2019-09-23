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
    <Table
      v-if="hasCompetencies"
      :data="achievements.items"
      :expandable-rows="true"
    >
      <template v-slot:HeaderRow="">
        <HeaderCell size="14">
          <h4>{{ $str('competencies', 'criteria_childcompetency') }}</h4>
        </HeaderCell>
        <HeaderCell size="2" class="tui-criteriaChildcompetency__progress">
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
        <p
          class="tui-criteriaChildcompetency__summary"
          v-html="row.competency.description"
        />
        <div v-if="row.assigned">
          <a
            :href="
              $url('/totara/competency/profile/details/', {
                competency_id: row.competency.id,
                user_id: userId,
              })
            "
            class="btn btn-primary"
          >
            {{ $str('view_competency', 'criteria_childcompetency') }}
          </a>
        </div>
        <div v-else>
          <a
            href="#"
            class="btn btn-primary"
            @click.prevent="assignCompetency(row.competency)"
          >
            {{
              $str(
                achievements.current_user
                  ? 'self_assign_competency'
                  : 'assign_competency',
                'criteria_childcompetency'
              )
            }}
          </a>
        </div>
      </template>
    </Table>
    <div v-else-if="!$apollo.loading">
      <h4>{{ $str('competencies', 'criteria_childcompetency') }}</h4>
      <p>{{ $str('no_competencies', 'criteria_childcompetency') }}</p>
    </div>
  </div>
</template>

<script>
import CreateUserAssignmentMutation from '../../../../../competency/webapi/ajax/create_user_assignments.graphql';
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
        if (current.value && current.value.proficient) {
          total += 1;
        }

        return total;
      }, 0);
    },

    hasCompetencies() {
      return this.achievements.items.length > 0;
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

  methods: {
    assignCompetency(competency) {
      const confirmMsg = `Would you like to assign ${competency.fullname}?`;

      let result = confirm(confirmMsg);
      if (result) {
        this.$apollo
          .mutate({
            // Query
            mutation: CreateUserAssignmentMutation,
            // Parameters
            variables: {
              user_id: this.userId,
              competency_ids: [competency.id],
            },
          })
          .then(({ data }) => {
            if (data && data.totara_competency_create_user_assignments) {
              let result = data.totara_competency_create_user_assignments;
              if (result.length > 0) {
                this.$apollo.queries.achievements.refetch();
                alert('Competency has been assigned successfully');
              }
              // TODO Handle case when no result is returned
            }
          })
          .catch(error => {
            alert('Unfortunately there was an error assigning competency');
            console.log('error');
            console.error(error);
          });
      }
    },
  },
};
</script>
<style lang="scss">
.tui-criteriaChildcompetency {
  &__summary {
    margin-top: 10px;
    margin-bottom: 30px;
  }

  &__progress {
    text-align: right;
  }
}
</style>
<lang-strings>
  {
    "criteria_childcompetency": [
      "competencies",
      "no_competencies",
      "required_only",
      "view_competency",
      "assign_competency",
      "self_assign_competency"
    ]
  }

</lang-strings>
