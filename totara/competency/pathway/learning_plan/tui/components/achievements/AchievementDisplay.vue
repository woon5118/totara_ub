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
  <div class="tui-pathwayLearningPlanAchievement">
    <!-- String if no plans available -->
    <div v-if="!hasPlans" class="tui-pathwayLearningPlanAchievement__empty">
      {{ $str('no_available_learning_plans', 'pathway_learning_plan') }}
    </div>

    <!-- Learning plan proficiency content -->
    <div v-else class="tui-pathwayLearningPlanAchievement__content">
      <AchievementLayout>
        <!-- Learning plan proficiency left content -->
        <template v-slot:left>
          <div class="tui-pathwayLearningPlanAchievement__overview">
            <h5 class="tui-pathwayLearningPlanAchievement__title">
              {{
                $str('achievement_via_learning_plan', 'pathway_learning_plan')
              }}
            </h5>
            <template v-if="hasValue">
              <div class="tui-pathwayLearningPlanAchievement__value">
                <span class="tui-pathwayLearningPlanAchievement__value-title">
                  {{ plans.scale_value.name }}
                </span>
                {{ $str('set_on', 'pathway_learning_plan', plans.date) }}
              </div>
            </template>
            <template v-else>
              <div class="tui-pathwayLearningPlanAchievement__noValue">
                {{ $str('no_rating_set', 'pathway_learning_plan') }}
              </div>
            </template>
          </div>
        </template>

        <template v-slot:right>
          <Table
            :data="plans.learning_plans"
            :expandable-rows="true"
            class="tui-pathwayLearningPlanAchievement__list"
          >
            <template v-slot:row="{ row, expand, expandState }">
              <!-- learning plan, that can't be viewed -->
              <template v-if="!row.can_view">
                <ExpandCell :header="true" />
                <Cell size="11">
                  {{ $str('no_permission_view_plan', 'pathway_learning_plan') }}
                </Cell>
              </template>

              <template v-else>
                <!-- learning plan expand cell -->
                <ExpandCell
                  v-if="row.can_view"
                  :expand-state="expandState"
                  @click="expand()"
                />

                <!-- learning plan name cell -->
                <Cell
                  size="11"
                  :column-header="$str('name', 'pathway_learning_plan')"
                >
                  {{ row.name }}
                </Cell>
              </template>
            </template>

            <!-- Expanded row content -->
            <template v-slot:expand-content="{ row }">
              <div class="tui-pathwayLearningPlanAchievement__summary">
                <h6 class="tui-pathwayLearningPlanAchievement__summary-header">
                  {{ row.name }}
                </h6>
                <div
                  v-if="row.description"
                  class="tui-pathwayLearningPlanAchievement__summary-body"
                  v-html="row.description"
                />

                <ActionLink
                  :href="getPlanUrl(row.id)"
                  :text="$str('view_plan', 'pathway_learning_plan')"
                  :class="'tui-pathwayLearningPlanAchievement__summary-button'"
                  :styleclass="{
                    primary: true,
                    small: true,
                  }"
                />
              </div>
            </template>
          </Table>
        </template>
      </AchievementLayout>
    </div>
  </div>
</template>

<script>
// Components
import AchievementLayout from 'totara_competency/components/achievements/AchievementLayout';
import ActionLink from 'totara_core/components/links/ActionLink';
import Cell from 'totara_core/components/datatable/Cell';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import Table from 'totara_core/components/datatable/Table';
// GraphQL
import CompetencyPlansQuery from 'pathway_learning_plan/graphql/competency_plans';

export default {
  components: {
    AchievementLayout,
    ActionLink,
    Cell,
    ExpandCell,
    Table,
  },

  inheritAttrs: false,

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
          assignment_id: this.assignmentId,
          user_id: this.userId,
        };
      },
      update({ pathway_learning_plan_competency_plans: plans }) {
        this.$emit('loaded');
        return plans;
      },
    },
  },

  computed: {
    /**
     * Check if data contains learning plan
     *
     * @return {Boolean}
     */
    hasPlans() {
      return this.plans.learning_plans;
    },

    /**
     * Check if a scale value has been set
     *
     * @return {Boolean}
     */
    hasValue() {
      return this.hasPlans && this.plans.scale_value != null;
    },
  },

  methods: {
    /**
     * Return URL for plan
     *
     * @param {Integer} planId
     * @return {String}
     */
    getPlanUrl(planId) {
      return this.$url('/totara/plan/component.php', {
        c: 'competency',
        id: planId,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_learning_plan" : [
      "achievement_via_learning_plan",
      "name",
      "no_available_learning_plans",
      "no_rating_set",
      "no_permission_view_plan",
      "set_on",
      "view_plan",
      "work_towards_level"
    ]
  }
</lang-strings>
