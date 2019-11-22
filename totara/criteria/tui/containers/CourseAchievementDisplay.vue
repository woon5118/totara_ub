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
  @package totara_criteria
-->

<template>
  <div>
    <div v-if="!$apollo.loading && !hasCourses">
      <h4>{{ $str('courses', 'totara_criteria') }}</h4>
      <p>{{ $str('no_courses', 'totara_criteria') }}</p>
    </div>
    <template v-if="hasCourses">
      <Table :data="achievements.items" :expandable-rows="true">
        <template v-slot:header-row="">
          <HeaderCell size="14">
            <h4>{{ $str('courses', 'totara_criteria') }}</h4>
          </HeaderCell>
          <HeaderCell size="2" class="tui-criteriaLinkedcourses__progress">
            <div>{{ completedNumberOfCourses }} / {{ numberOfCourses }}</div>
            <div v-if="!hasAggregationAll">
              {{
                $str(
                  'courses_required',
                  'totara_criteria',
                  achievements.required_items
                )
              }}
            </div>
          </HeaderCell>
        </template>
        <template v-slot:row="{ row, expand }">
          <Cell size="1" style="text-align: center;">
            <CheckIcon v-if="isComplete(row)" size="300" />
          </Cell>

          <Cell size="13">
            <a v-if="row.course" href="#" @click.prevent="expand()">
              {{ getCourseName(row) }}
            </a>
            <template v-else>{{ getCourseName(row) }}</template>
          </Cell>

          <Cell size="2">
            <div
              v-if="hasProgress(row)"
              class="progress progress-striped active"
            >
              <div
                class="bar"
                role="progressbar"
                aria-valuemin="0"
                aria-valuemax="100"
                :aria-valuenow="row.progress"
                :style="width(row)"
              >
                <span class="progressbar__text"
                  >{{ row.course.progress }}%</span
                >
              </div>
            </div>
          </Cell>
        </template>

        <template v-slot:expand-content="{ row }">
          <h4>{{ row.course.fullname }}</h4>
          <p
            class="tui-criteriaCourseCompletion__summary"
            v-html="row.course.description"
          />
          <a :href="row.course.url_view" class="btn btn-primary">
            {{ $str('course_link', 'totara_criteria') }}
          </a>
        </template>
      </Table>
    </template>
  </div>
</template>

<script>
import Cell from 'totara_core/presentation/datatable/Cell';
import CheckIcon from 'totara_core/presentation/icons/common/CheckSuccess';
import HeaderCell from 'totara_core/presentation/datatable/HeaderCell';
import Table from 'totara_core/presentation/datatable/Table';

export default {
  components: { Cell, CheckIcon, HeaderCell, Table },
  props: {
    achievements: {
      required: true,
      type: Object,
    },
  },

  computed: {
    numberOfCourses() {
      return this.achievements.items ? this.achievements.items.length : 0;
    },
    hasCourses() {
      return this.numberOfCourses > 0;
    },
    completedNumberOfCourses() {
      if (!this.hasCourses) {
        return 0;
      }

      let complete = 0;

      this.achievements.items.forEach(item => {
        if (item.course && item.course.progress === 100) {
          complete = complete + 1;
        }
      });

      return complete;
    },
    hasAggregationAll() {
      return this.achievements.aggregation_method === 1;
    },
  },

  methods: {
    width(row) {
      return {
        width: row.course.progress + '%',
      };
    },
    getCourseName(row) {
      return row.course
        ? row.course.fullname
        : this.$str('hidden_course', 'totara_criteria');
    },
    hasProgress(row) {
      return row.course && row.course.progress > 0;
    },
    isComplete(row) {
      return row.course && row.course.progress === 100;
    },
  },
};
</script>

<style lang="scss">
.tui-criteriaLinkedcourses {
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
    "totara_criteria": [
      "course_link",
      "courses",
      "courses_required",
      "hidden_course",
      "no_courses"
    ]
  }

</lang-strings>
