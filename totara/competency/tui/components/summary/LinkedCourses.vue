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
  <div class="tui-competencyOverviewLinkedCourses">
    <div class="tui-competencyOverviewLinkedCourses__header">
      <div class="tui-competencyOverviewLinkedCourses__header_title">
        {{ $str('linked_courses', 'totara_competency') }}
      </div>
      <a
        :href="editUrl"
        class="tui-competencyOverviewLinkedCourses__header_edit"
        :title="$str('edit', 'moodle')"
      >
        <FlexIcon icon="edit" size="200" :alt="$str('edit', 'moodle')" />
      </a>
    </div>
    <div
      v-if="noCoursesLinked"
      class="tui-competencyOverviewLinkedCourses__noCourses"
    >
      {{ $str('no_courses_linked_yet', 'totara_competency') }}
    </div>
    <div v-else class="tui-competencyOverviewLinkedCourses__list" role="grid">
      <div
        v-for="(course, id) in data"
        :key="id"
        class="tui-competencyOverviewLinkedCourses__list_row"
        role="grid"
      >
        <span>
          <a :href="courseUrl(course.course_id)" :title="course.fullname">
            {{ course.fullname }}
          </a>
        </span>
        <span v-if="course.is_mandatory">
          {{ $str('mandatory', 'totara_competency') }}
        </span>
        <span v-else>
          {{ $str('optional', 'totara_competency') }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';

import LinkedCoursesQuery from 'totara_competency/graphql/linked_courses';

export default {
  components: { FlexIcon },

  props: {
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      data: [],
    };
  },

  computed: {
    noCoursesLinked() {
      return this.data.length === 0;
    },

    editUrl() {
      return this.$url('/totara/competency/competency_edit.php', {
        s: 'linkedcourses',
        id: this.competencyId,
      });
    },
  },

  methods: {
    courseUrl(course_id) {
      return this.$url('/course/view.php', {
        id: course_id,
      });
    },
  },

  apollo: {
    data: {
      query: LinkedCoursesQuery,
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update: data => data.totara_competency_linked_courses,
    },
  },
};
</script>

<style lang="scss">
.tui-competencyOverviewLinkedCourses {
  padding-top: var(--tui-gap-4);
  &__header {
    margin-bottom: var(--tui-gap-2);
    padding-bottom: var(--tui-gap-1);
    border-bottom: 1px solid var(--tui-color-neutral-5);

    &_title {
      display: inline-block;
      margin-top: auto;
      margin-bottom: auto;
      margin-left: var(--tui-gap-2);
      font-weight: bold;
      font-size: var(--tui-font-size-18);
    }

    &_edit {
      float: right;
      margin-bottom: var(--tui-gap-4);
      padding-left: var(--tui-gap-2);
    }
  }

  &__noCourses {
    padding: var(--tui-font-size-8);
    font-style: italic;
  }

  &__list {
    padding: var(--tui-font-size-8) 0;
    &_row {
      display: flex;
      flex-direction: column;
      margin-bottom: var(--tui-font-size-12);
      padding-right: var(--tui-font-size-8);
      padding-bottom: var(--tui-font-size-12);
      padding-left: var(--tui-font-size-8);
      border-bottom: var(--tui-font-size-1) solid var(--tui-color-neutral-4);

      &:first-child {
        padding-top: var(--tui-font-size-12);
        border-top: var(--tui-font-size-1) solid var(--tui-color-neutral-4);
      }

      span {
        display: inline-block;
        width: 50%;
      }

      @media (min-width: $tui-screen-sm) {
        flex-direction: row;
      }
    }
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "edit"
    ],
    "totara_competency": [
      "linked_courses",
      "no_courses_linked_yet",
      "mandatory",
      "optional"
    ]
  }
</lang-strings>
