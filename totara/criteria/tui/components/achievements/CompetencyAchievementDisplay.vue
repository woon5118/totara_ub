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
  @author Marco Song <marco.song@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_criteria
-->

<template>
  <div class="tui-criteriaCompetencyAchievement">
    <AchievementLayout>
      <template v-slot:left>
        <div class="tui-criteriaCompetencyAchievement__goal">
          <!-- Proficiency goal title -->
          <h5 class="tui-criteriaCompetencyAchievement__title">
            {{ $str('achieve_proficiency_in_competencies', 'totara_criteria') }}
          </h5>

          <!-- Proficiency progress circle -->
          <ProgressCircle
            :complete="true"
            :completed="
              achievedCompetencies >= numberOfRequiredCompetencies
                ? numberOfRequiredCompetencies
                : achievedCompetencies
            "
            :target="numberOfRequiredCompetencies"
          />
        </div>
      </template>
      <template v-slot:right>
        <Table
          :data="achievements.items"
          :expandable-rows="true"
          :no-items-text="noCompetenciesString"
        >
          <template v-slot:row="{ row, expand, expandState }">
            <!-- Competency details expand cell -->
            <ExpandCell
              size="1"
              :expand-state="expandState"
              @click="expand()"
            />

            <!-- Competency name cell -->
            <Cell
              size="9"
              :column-header="$str('competencies', 'totara_criteria')"
            >
              {{ row.competency.fullname }}
            </Cell>

            <!-- Competency achivement level cell -->
            <Cell
              size="3"
              :class="'tui-criteriaCompetencyAchievement__level'"
              :column-header="$str('achievement_level', 'totara_criteria')"
            >
              <template v-if="row.value">
                {{ row.value.name }}
              </template>
              <template v-else>
                <span
                  class="tui-criteriaCompetencyAchievement__level-notAvailable"
                >
                  {{ $str('not_available', 'totara_criteria') }}
                </span>
              </template>
            </Cell>

            <!-- Competency completion cell -->
            <Cell
              size="3"
              :column-header="$str('completion', 'totara_criteria')"
              align="end"
            >
              <div
                v-if="row.value && row.value.proficient"
                class="tui-criteriaCompetencyAchievement__completion-complete"
              >
                <CheckIcon size="200" />
                {{ $str('complete', 'totara_criteria') }}
              </div>
              <div
                v-else
                class="tui-criteriaCompetencyAchievement__completion-notComplete"
              >
                {{ $str('not_complete', 'totara_criteria') }}
              </div>
            </Cell>
          </template>

          <!-- Competency expanded row -->
          <template v-slot:expand-content="{ row }">
            <div class="tui-criteriaCompetencyAchievement__summary">
              <h6 class="tui-criteriaCompetencyAchievement__summary-header">
                {{ row.competency.fullname }}
              </h6>
              <div
                class="tui-criteriaCompetencyAchievement__summary-body"
                v-html="row.competency.description"
              />

              <!-- Display view competency link-->
              <ActionLink
                v-if="row.assigned"
                :href="
                  $url('/totara/competency/profile/details/', {
                    competency_id: row.competency.id,
                    user_id: userId,
                  })
                "
                :text="$str('view_competency', 'totara_criteria')"
                :class="'tui-criteriaCompetencyAchievement__summary-button'"
                :styleclass="{
                  primary: true,
                  small: true,
                }"
              />

              <!-- Display self assign competency button-->
              <div v-else-if="row.self_assignable">
                <Button
                  :text="
                    $str(
                      achievements.current_user
                        ? 'self_assign_competency'
                        : 'assign_competency',
                      'totara_criteria'
                    )
                  "
                  :styleclass="{
                    primary: true,
                    small: true,
                  }"
                  :class="'tui-criteriaCompetencyAchievement__summary-button'"
                  @click="showModal(row.competency)"
                />
              </div>
            </div>

            <!-- Display self assign competency modal-->
            <ConfirmationModal
              :open="modalOpen"
              :title="
                $str('confirm_assign_competency_title', 'totara_criteria')
              "
              @confirm="assignCompetency(row.competency)"
              @cancel="closeModal"
            >
              {{
                $str(
                  'confirm_assign_competency_body',
                  'totara_criteria',
                  row.competency.fullname
                )
              }}
            </ConfirmationModal>
          </template>
        </Table>
      </template>
    </AchievementLayout>
  </div>
</template>

<script>
// Components
import AchievementLayout from 'totara_competency/components/achievements/AchievementLayout';
import ActionLink from 'totara_core/components/links/ActionLink';
import Button from 'totara_core/components/buttons/Button';
import Cell from 'totara_core/components/datatable/Cell';
import CheckIcon from 'totara_core/components/icons/common/CheckSuccess';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import ProgressCircle from 'totara_competency/components/achievements/ProgressCircle';
import Table from 'totara_core/components/datatable/Table';
// GraphQL
import CreateUserAssignmentMutation from 'totara_competency/graphql/create_user_assignments';

export default {
  components: {
    AchievementLayout,
    ActionLink,
    Button,
    Cell,
    CheckIcon,
    ConfirmationModal,
    ExpandCell,
    ProgressCircle,
    Table,
  },

  props: {
    achievements: {
      required: true,
      type: Object,
    },
    type: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      modalOpen: false,
    };
  },

  computed: {
    /**
     * Return int for number of completed competencies
     *
     * @return {Integer}
     */
    achievedCompetencies() {
      return this.achievements.items.reduce((total, current) => {
        return current.value && current.value.proficient ? (total += 1) : total;
      }, 0);
    },

    /**
     * Return no competency strings based on competency type
     *
     * @return {String}
     */
    noCompetenciesString() {
      if (this.type === 'otherCompetency') {
        return this.$str('no_competencies', 'criteria_othercompetency');
      }
      return this.$str('no_competencies', 'criteria_childcompetency');
    },

    /**
     * Return int for required number of competencies completed to fulfill criteria
     *
     * @return {Integer}
     */
    numberOfRequiredCompetencies() {
      if (this.achievements.aggregation_method === 1) {
        return this.achievements.items.length;
      }
      return this.achievements.required_items;
    },
  },

  methods: {
    /**
     * Trigger a mutation to assign selected competency
     *
     */
    assignCompetency(competency) {
      this.$apollo
        .mutate({
          // Query
          mutation: CreateUserAssignmentMutation,
          // Parameters
          variables: {
            competency_ids: [competency.id],
            user_id: this.userId,
          },
        })
        .then(({ data }) => {
          if (data && data.totara_competency_create_user_assignments) {
            let result = data.totara_competency_create_user_assignments;
            if (result.length > 0) {
              this.$emit('self-assigned');
            } else {
              // TODO Handle case when no result is returned
            }
          }
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => this.closeModal());
    },

    /**
     * Show assign competency modal
     *
     */
    showModal() {
      this.modalOpen = true;
    },

    /**
     * Close assign competency modal
     *
     */
    closeModal() {
      this.modalOpen = false;
    },
  },
};
</script>

<lang-strings>
  {
    "criteria_childcompetency": [
      "no_competencies"
    ],
    "criteria_othercompetency": [
      "no_competencies"
    ],
    "totara_criteria": [
      "achieve_proficiency_in_competencies",
      "assign_competency",
      "competencies",
      "complete",
      "completion",
      "confirm_assign_competency_body",
      "confirm_assign_competency_title",
      "not_available",
      "not_complete",
      "achievement_level",
      "self_assign_competency",
      "view_competency"
    ]
  }
</lang-strings>
