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
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_criteria
-->

<template>
  <div class="tui-criteriaCourseAchievement">
    <!-- Critria overview & course list -->
    <AchievementLayout>
      <template v-slot:left>
        <div class="tui-criteriaCourseAchievement__goal">
          <!-- Proficiency goal title -->
          <h5 class="tui-criteriaCourseAchievement__title">
            {{
              $str('achieve_proficiency_in_linked_courses', 'totara_criteria')
            }}
          </h5>

          <!-- Proficiency progress circle -->
          <ProgressCircle
            :complete="criteriaFulfilled"
            :completed="
              completedNumberOfCourses >= targetNumberOfCourses
                ? targetNumberOfCourses
                : completedNumberOfCourses
            "
            :target="targetNumberOfCourses"
          />
        </div>
      </template>

      <template v-slot:right>
        <Table
          :data="achievements.items"
          :expandable-rows="true"
          :no-items-text="$str('no_courses', 'totara_criteria')"
        >
          <template v-slot:row="{ row, expand, expandState }">
            <!-- Course details expand cell -->
            <ExpandCell
              size="1"
              :expand-state="expandState"
              @click="expand()"
            />

            <!-- Course name cell -->
            <Cell size="9" :column-header="$str('courses', 'totara_criteria')">
              {{ getCourseName(row) }}
            </Cell>

            <!-- Course progress cell -->
            <Cell
              size="3"
              :class="'tui-criteriaCourseAchievement__progress'"
              :column-header="$str('progress', 'totara_criteria')"
            >
              <div
                v-if="hasProgress(row)"
                class="tui-criteriaCourseAchievement__progress-bar"
              >
                <Progress :value="row.course.progress" />
              </div>
              <div v-else class="tui-criteriaCourseAchievement__progress-empty">
                {{ $str('not_available', 'totara_criteria') }}
              </div>
            </Cell>

            <!-- Course completion cell -->
            <Cell
              size="3"
              :column-header="$str('completion', 'totara_criteria')"
              align="end"
            >
              <div
                v-if="isComplete(row)"
                class="tui-criteriaCourseAchievement__completion-complete"
              >
                <CheckIcon size="200" />
                {{ $str('complete', 'totara_criteria') }}
              </div>
              <div
                v-else
                class="tui-criteriaCourseAchievement__completion-notComplete"
              >
                {{ $str('not_complete', 'totara_criteria') }}
              </div>
            </Cell>
          </template>

          <template v-slot:expand-content="{ row }">
            <div class="tui-criteriaCourseAchievement__summary">
              <h6 class="tui-criteriaCourseAchievement__summary-header">
                {{ row.course.fullname }}
              </h6>
              <div
                class="tui-criteriaCourseAchievement__summary-body"
                v-html="row.course.description"
              />

              <ActionLink
                :href="row.course.url_view"
                :text="$str('course_link', 'totara_criteria')"
                :class="'tui-criteriaCourseAchievement__summary-button'"
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
</template>

<script>
import AchievementLayout from 'totara_competency/components/achievements/AchievementLayout';
import ActionLink from 'totara_core/components/links/ActionLink';
import Cell from 'totara_core/components/datatable/Cell';
import CheckIcon from 'totara_core/components/icons/common/CheckSuccess';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import Progress from 'totara_core/components/progress/Progress';
import ProgressCircle from 'totara_competency/components/achievements/ProgressCircle';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    AchievementLayout,
    ActionLink,
    Cell,
    CheckIcon,
    ExpandCell,
    Progress,
    ProgressCircle,
    Table,
  },

  props: {
    achievements: {
      required: true,
      type: Object,
    },
  },

  computed: {
    /**
     * Return bool for criteria fulfilled
     *
     * @return {Boolean}
     */
    criteriaFulfilled() {
      return this.completedNumberOfCourses >= this.targetNumberOfCourses;
    },

    /**
     * Return int for number of courses
     *
     * @return {Integer}
     */
    numberOfCourses() {
      return this.achievements.items ? this.achievements.items.length : 0;
    },

    /**
     * Return int for number of courses completed
     *
     * @return {Integer}
     */
    completedNumberOfCourses() {
      let complete = 0;

      if (!this.numberOfCourses) {
        return complete;
      }

      this.achievements.items.forEach(item => {
        if (item.course && item.course.progress === 100) {
          complete++;
        }
      });

      return complete;
    },

    /**
     * Return int for required number of courses completed to fulfill criteria
     *
     * @return {Integer}
     */
    targetNumberOfCourses() {
      // If aggregation_method is set to achieve ALL courses
      if (this.achievements.aggregation_method === 1) {
        return this.numberOfCourses;
      }
      return this.achievements.required_items;
    },
  },

  methods: {
    /**
     * Return course name or unavilable to user string
     *
     * @return {String}
     */
    getCourseName(row) {
      return row.course
        ? row.course.fullname
        : this.$str('hidden_course', 'totara_criteria');
    },

    /**
     * Return bool based on progress data
     *
     * @return {Boolean}
     */
    hasProgress(row) {
      return row.course && row.course.progress > 0;
    },

    /**
     * Return bool based on course completion
     *
     * @return {Boolean}
     */
    isComplete(row) {
      return row.course && row.course.progress === 100;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_criteria": [
      "achieve_proficiency_in_linked_courses",
      "complete",
      "completion",
      "course_link",
      "courses",
      "hidden_course",
      "no_courses",
      "not_available",
      "not_complete",
      "progress"
    ]
  }

</lang-strings>
