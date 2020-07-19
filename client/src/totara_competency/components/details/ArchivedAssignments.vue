<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_competency
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
import Loader from 'tui/components/loader/Loader';
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
