<template>
  <Loader :loading="$apollo.loading">
    <Table v-if="!$apollo.loading" :data="subjectInstances">
      <template v-slot:header-row>
        <HeaderCell :size="showSubjectName ? '8' : '10'">
          {{ $str('user_activities:title_header', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell v-if="showSubjectName" size="2">
          {{ $str('user_activities:subject_header', 'mod_perform') }}
        </HeaderCell>
        <HeaderCell size="2">
          {{ $str('perform:view:status', 'mod_perform') }}
        </HeaderCell>
      </template>
      <template v-slot:row="{ row: subjectInstance }">
        <Cell
          :size="showSubjectName ? '8' : '10'"
          :column-header="$str('user_activities:title_header', 'mod_perform')"
        >
          <a href="view/1">{{ subjectInstance.activity.name }}</a>
        </Cell>
        <Cell
          v-if="showSubjectName"
          size="2"
          :column-header="$str('user_activities:subject_header', 'mod_perform')"
        >
          {{ subjectInstance.subject.fullname }}
        </Cell>
        <Cell
          size="2"
          :column-header="$str('user_activities:status_header', 'mod_perform')"
        >
          {{ getStatusText(subjectInstance) }}
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

import performSubjectInstancesQuery from 'mod_perform/graphql/subject_instances.graphql';

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
  },
  data() {
    return {
      subjectInstances: [],
    };
  },
  apollo: {
    subjectInstances: {
      query: performSubjectInstancesQuery,
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
  computed: {
    aboutFilter() {
      return [this.about.toUpperCase()];
    },
    showSubjectName() {
      return this.about === ABOUT_OTHERS;
    },
  },
  methods: {
    /**
     * Get the localized status text for a particular user activity.
     *
     * @param status {{String}}
     * @returns {string}
     */
    getStatusText({ status }) {
      switch (status) {
        case 'NOT_YET_STARTED':
          return this.$str(
            'user_activities:status_not_yet_started',
            'mod_perform'
          );
        case 'IN_PROGRESS':
          return this.$str('user_activities:status_in_progress', 'mod_perform');
        case 'COMPLETE':
          return this.$str('user_activities:status_complete', 'mod_perform');
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
      "perform:view:status",
      "user_activities:status_complete",
      "user_activities:status_header",
      "user_activities:status_in_progress",
      "user_activities:status_not_yet_started",
      "user_activities:subject_header",
      "user_activities:title_header"
    ]
  }
</lang-strings>
