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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <Loader :loading="$apollo.loading">
    <Table v-if="!$apollo.loading" :data="subjectInstances">
      <template v-slot:header-row>
        <HeaderCell :size="showSubjectName ? '6' : '8'">
          {{ $str('user_activities_title_header', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell v-if="showSubjectName" size="2">
          {{ $str('user_activities_subject_header', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2">
          {{
            $str('user_activities_status_header_participation', 'mod_perform')
          }}
        </HeaderCell>
        <HeaderCell size="2">
          {{ $str('user_activities_status_header_activity', 'mod_perform') }}
        </HeaderCell>
      </template>
      <template v-slot:row="{ row: subjectInstance }">
        <Cell
          :size="showSubjectName ? '6' : '8'"
          :column-header="$str('user_activities_title_header', 'mod_perform')"
        >
          <a :href="getViewActivityUrl(subjectInstance)">{{
            subjectInstance.activity.name
          }}</a>
        </Cell>
        <Cell
          v-if="showSubjectName"
          size="2"
          :column-header="$str('user_activities_subject_header', 'mod_perform')"
        >
          {{ subjectInstance.subject_user.fullname }}
        </Cell>
        <Cell
          size="2"
          :column-header="
            $str('user_activities_status_header_participation', 'mod_perform')
          "
        >
          {{
            getStatusText(
              subjectInstance.participant_instances[0].progress_status
            )
          }}
        </Cell>
        <Cell
          size="2"
          :column-header="
            $str('user_activities_status_header_activity', 'mod_perform')
          "
        >
          {{ getStatusText(subjectInstance.status) }}
        </Cell>
      </template>
    </Table>
  </Loader>
</template>
<script>
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Loader from 'totara_core/components/loader/Loader';
import Table from 'totara_core/components/datatable/Table';

import SubjectInstancesQuery from 'mod_perform/graphql/subject_instances.graphql';

const ABOUT_SELF = 'self';
const ABOUT_OTHERS = 'others';

export default {
  components: {
    Cell,
    HeaderCell,
    Loader,
    Table,
  },
  props: {
    about: {
      type: String,
      validator(val) {
        return [ABOUT_SELF, ABOUT_OTHERS].includes(val);
      },
    },
    viewUrl: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      subjectInstances: [],
    };
  },
  computed: {
    aboutFilter() {
      return [this.about.toUpperCase()];
    },
    showSubjectName() {
      return this.about === ABOUT_OTHERS;
    },
  },
  apollo: {
    subjectInstances: {
      query: SubjectInstancesQuery,
      fetchPolicy: 'network-only', // Always refetch data on tab change
      variables() {
        return {
          filters: {
            about: this.aboutFilter,
          },
        };
      },
      update: data => data['mod_perform_subject_instances'],
    },
  },
  methods: {
    /**
     * Get "view" url for a specific user activity.
     *
     * @param id {{Number}}
     * @returns {string}
     */
    getViewActivityUrl({ id }) {
      return `${this.viewUrl}?subject_instance_id=${id}`;
    },

    /**
     * Get the localized status text for a particular user activity.
     *
     * @param status {String}
     * @returns {string}
     */
    getStatusText(status) {
      switch (status) {
        case 'NOT_STARTED':
          return this.$str('user_activities_status_not_started', 'mod_perform');
        case 'IN_PROGRESS':
          return this.$str('user_activities_status_in_progress', 'mod_perform');
        case 'COMPLETE':
          return this.$str('user_activities_status_complete', 'mod_perform');
        default:
          return '';
      }
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "user_activities_status_complete",
      "user_activities_status_header_activity",
      "user_activities_status_header_participation",
      "user_activities_status_in_progress",
      "user_activities_status_not_started",
      "user_activities_subject_header",
      "user_activities_title_header",
      "toast_success_save_response",
      "toast_error_save_response"
    ]
  }
</lang-strings>
