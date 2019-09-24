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
  @package criteria_coursecompletion
-->

<template>
  <div>
    <Preloader :display="$apollo.loading" />
    <Table :data="achievements.items" :expandable-rows="true">
      <template v-slot:HeaderRow="">
        <HeaderCell size="14">
          <h4>{{ $str('courses', 'criteria_coursecompletion') }}</h4>
        </HeaderCell>
        <HeaderCell size="2" class="tui-criteriaCourseCompletion__progress">
          <div>{{ completedNumberOfCourses }} / {{ numberOfCourses }}</div>
          <div v-if="achievements.aggregation">
            {{
              $str(
                'courses_required',
                'criteria_coursecompletion',
                achievements.aggregation
              )
            }}
          </div>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row, expand }">
        <Cell size="1" style="text-align: center;">
          <CheckIcon v-if="row.progress === 100" size="300" />
        </Cell>

        <Cell size="13">
          <a v-if="row.course" href="#" @click.prevent="expand()">
            {{ getCourseName(row) }}
          </a>
          <template v-else>{{ getCourseName(row) }}</template>
        </Cell>

        <Cell size="2">
          <div v-if="hasProgress(row)" class="progress progress-striped active">
            <div
              class="bar"
              role="progressbar"
              aria-valuemin="0"
              aria-valuemax="100"
              :aria-valuenow="row.progress"
              :style="width(row)"
            >
              <span class="progressbar__text">{{ row.progress }}%</span>
            </div>
          </div>
        </Cell>
      </template>

      <template v-slot:expandContent="{ row }">
        <h4>{{ row.course.name }}</h4>
        <p
          class="tui-criteriaCourseCompletion__summary"
          v-html="row.course.summary"
        />
        <a :href="row.course.url" class="btn btn-primary">
          {{ $str('course_link', 'criteria_coursecompletion') }}
        </a>
      </template>
    </Table>
  </div>
</template>

<script>
import AchievementsQuery from '../../webapi/ajax/achievements.graphql';
import Preloader from 'totara_competency/presentation/Preloader';
import Cell from 'totara_core/presentation/datatable/Cell';
import CheckIcon from 'totara_core/presentation/icons/common/CheckSuccess';
import HeaderCell from 'totara_core/presentation/datatable/HeaderCell';
import Table from 'totara_core/presentation/datatable/Table';

export default {
  components: { Preloader, Cell, CheckIcon, HeaderCell, Table },
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
    numberOfCourses() {
      return this.achievements.items.length;
    },
    completedNumberOfCourses() {
      let complete = 0;

      this.achievements.items.forEach(item => {
        if (item.progress === 100) {
          complete = complete + 1;
        }
      });

      return complete;
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
      update({ criteria_coursecompletion_achievements: achievements }) {
        return achievements;
      },
    },
  },

  methods: {
    width(row) {
      return {
        width: row.progress + '%',
      };
    },
    getCourseName(row) {
      var courseName = this.$str('hidden_course', 'criteria_coursecompletion');
      if (row.course) {
        courseName = row.course.name;
      }
      return courseName;
    },
    hasProgress(row) {
      return row.course && row.progress > 0;
    },
  },
};
</script>

<style lang="scss">
.tui-criteriaCourseCompletion {
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
    "criteria_coursecompletion": [
      "course_link",
      "courses",
      "hidden_course",
      "courses_required"
    ]
  }

</lang-strings>
