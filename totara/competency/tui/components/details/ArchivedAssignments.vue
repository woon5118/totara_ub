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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyDetailArchivedAssignments">
    <h4 class="tui-competencyDetailArchivedAssignments__title">
      {{ $str('archived_assignments', 'totara_competency') }}
    </h4>

    <Loader :loading="$apollo.loading">
      <ArchivedAssignmentsTable :assignments="archivedAssignmentList" />
    </Loader>
  </div>
</template>

<script>
// Components
import ArchivedAssignmentsTable from 'totara_competency/components/details/ArchivedAssignmentsTable';
import Loader from 'totara_core/components/loader/Loader';
// GraphQL
import CompetencyProfileDetailsQuery from 'totara_competency/graphql/profile_competency_details';

export default {
  components: {
    ArchivedAssignmentsTable,
    Loader,
  },
  props: {
    userId: {
      required: true,
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      data: {
        competency: {},
        items: [],
      },
    };
  },

  /**
   * Fetch competency details assignments
   *
   */
  apollo: {
    data: {
      query: CompetencyProfileDetailsQuery,
      variables() {
        return {
          user_id: this.userId,
          competency_id: this.competencyId,
        };
      },
      update({
        totara_competency_profile_competency_details: { competency, items },
      }) {
        return { competency, items };
      },
    },
  },

  computed: {
    /**
     * Create an array of archieved assignments
     *
     * @return {Array}
     */
    archivedAssignmentList() {
      let archivedAssignmentList = [];

      // Filter for only archived assignments
      archivedAssignmentList = this.data.items
        .filter(function(elem) {
          return (
            elem.assignment.archived_at || elem.assignment.type === 'legacy'
          );
        })
        .map(function(elem) {
          return {
            name: elem.assignment.progress_name,
            archivedAt: elem.assignment.archived_at,
            legacy: elem.assignment.type === 'legacy',
            proficient: elem.my_value.proficient,
          };
        });
      return archivedAssignmentList;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "archived_assignments"
    ]
  }
</lang-strings>
