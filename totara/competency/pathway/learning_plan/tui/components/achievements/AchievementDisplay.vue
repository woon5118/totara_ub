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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package pathway_learning_plan
-->

<template>
  <div>
    <div
      v-if="hasPlans"
      class="tui-pathwayLearningPlan-achievementDisplay__container"
    >
      <Table
        :data="plans.learning_plans"
        :expandable-rows="true"
        class="tui-pathwayLearningPlan-achievementDisplay__plans"
      >
        <template v-slot:row="{ row, expand, expandState }">
          <!-- learning plan expand cell -->
          <ExpandCell
            v-if="row.can_view"
            size="1"
            :expand-state="expandState"
            @click="expand()"
          />
          <Cell v-else size="1" />

          <Cell>
            <span v-if="row.can_view">
              {{ row.name }}
            </span>
            <span>
              {{ $str('no_permission_view_plan', 'pathway_learning_plan') }}
            </span>
          </Cell>
        </template>

        <!-- Expanded row content -->
        <template v-slot:expand-content="{ row }">
          <h4>{{ row.name }}</h4>

          <div>
            <span v-if="hasDescription(row)" v-html="row.description" />
            <span
              v-else
              class="tui-pathwayLearningPlan-achievementDisplay--none"
            >
              {{ $str('no_description_available', 'pathway_learning_plan') }}
            </span>
          </div>

          <a
            :href="getPlanUrl(row.id)"
            class="tui-pathwayLearningPlan-achievementDisplay--padTop btn btn-primary"
          >
            {{ $str('view_plan', 'pathway_learning_plan') }}
          </a>
        </template>
      </Table>
      <div class="tui-pathwayLearningPlan-achievementDisplay__value">
        <div
          v-if="hasValue"
          class="tui-pathwayLearningPlan-achievementDisplay__value_text"
        >
          {{ plans.date }}
        </div>
        <div
          v-if="hasValue"
          class="tui-pathwayLearningPlan-achievementDisplay__value_text tui-pathwayLearningPlan-achievementDisplay__value_name"
        >
          {{ plans.scale_value.name }}
        </div>
        <div
          v-if="!hasValue"
          class="tui-pathwayLearningPlan-achievementDisplay__value_text tui-pathwayLearningPlan-achievementDisplay--none"
        >
          {{ $str('no_rating_set', 'pathway_learning_plan') }}
        </div>
      </div>
    </div>
    <div
      v-else
      class="tui-pathwayLearningPlan-achievementDisplay--padTop tui-pathwayLearningPlan-achievementDisplay--none"
    >
      {{ $str('no_available_learning_plans', 'pathway_learning_plan') }}
    </div>
  </div>
</template>

<script>
// Components
import Cell from 'totara_core/components/datatable/Cell';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import Table from 'totara_core/components/datatable/Table';
// GraphQL
import CompetencyPlansQuery from 'pathway_learning_plan/graphql/competency_plans';

export default {
  components: {
    Cell,
    ExpandCell,
    Table,
  },

  props: {
    assignmentId: {
      required: true,
      type: Number,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      plans: [],
    };
  },

  apollo: {
    plans: {
      query: CompetencyPlansQuery,
      context: { batch: true },
      variables() {
        return {
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ pathway_learning_plan_competency_plans: plans }) {
        this.$emit('loaded');
        return plans;
      },
    },
  },

  computed: {
    hasPlans() {
      return this.plans.learning_plans && this.plans.learning_plans.length > 0;
    },

    hasValue() {
      return this.hasPlans && this.plans.scale_value != null;
    },
  },

  methods: {
    hasDescription(plan) {
      return plan.description && plan.description.length > 0;
    },

    getPlanUrl(planId) {
      return this.$url('/totara/plan/component.php', {
        id: planId,
        c: 'competency',
      });
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayLearningPlan-achievementDisplay {
  &__container {
    @media (min-width: $tui-screen-sm) {
      display: flex;
    }
  }

  &__plans {
    @media (min-width: $tui-screen-sm) {
      flex-grow: 1;
    }
  }

  &__value {
    @media (min-width: $tui-screen-sm) {
      min-width: 20%;
      max-width: 40%;
      margin-top: var(--tui-gap-2);
      margin-left: auto;
      padding-left: var(--tui-gap-2);

      &_text {
        text-align: right;
      }
    }

    @media (max-width: $tui-screen-sm) {
      padding-top: var(--tui-gap-2);
    }

    &_name {
      font-weight: bold;
    }
  }

  &--none {
    font-style: italic;
  }

  &--padTop {
    margin-top: var(--tui-gap-2);
  }
}
</style>

<lang-strings>
  {
    "pathway_learning_plan" : [
      "no_available_learning_plans",
      "no_description_available",
      "no_rating_set",
      "no_permission_view_plan",
      "view_plan"
    ]
  }
</lang-strings>
