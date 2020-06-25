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
    <div class="tui-competencySummary__sectionHeader">
      <h3 class="tui-competencySummary__sectionHeader-title">
        {{ $str('linked_courses', 'totara_competency') }}
      </h3>
      <a
        :href="editUrl"
        class="tui-competencySummary__sectionHeader-edit"
        :title="$str('edit', 'moodle')"
      >
        <EditIcon :size="200" :alt="$str('edit', 'moodle')" />
      </a>
    </div>

    <Table
      :data="data"
      :border-bottom-hidden="true"
      :border-top-hidden="true"
      :border-separator-hidden="true"
      :hover-off="true"
      :no-items-text="$str('no_courses_linked_yet', 'totara_competency')"
    >
      <template v-slot:row="{ row }">
        <Cell size="6" valign="center">
          <a :href="courseUrl(row.course_id)">
            {{ row.fullname }}
          </a>
        </Cell>
        <Cell size="10" valign="center">
          <span>
            {{
              $str(
                row.is_mandatory ? 'mandatory' : 'optional',
                'totara_competency'
              )
            }}
          </span>
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
// Components
import Cell from 'totara_core/components/datatable/Cell';
import EditIcon from 'totara_core/components/icons/common/Edit';
import Table from 'totara_core/components/datatable/Table';
// Query
import linkedCoursesQuery from 'totara_competency/graphql/linked_courses';

export default {
  components: {
    Cell,
    EditIcon,
    Table,
  },

  props: {
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      data: [],
      editUrl: '',
    };
  },

  apollo: {
    data: {
      query: linkedCoursesQuery,
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update({ totara_competency_linked_courses: data }) {
        this.editUrl = this.$url('/totara/competency/competency_edit.php', {
          s: 'linkedcourses',
          id: this.competencyId,
        });
        return data;
      },
    },
  },

  methods: {
    courseUrl(course_id) {
      return this.$url('/course/view.php', {
        id: course_id,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "edit"
    ],
    "totara_competency": [
      "linked_courses",
      "mandatory",
      "no_courses_linked_yet",
      "optional"
    ]
  }
</lang-strings>
