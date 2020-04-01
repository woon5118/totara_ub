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
          <a :href="getViewActivityUrl(subjectInstance)">{{
            subjectInstance.activity.name
          }}</a>
        </Cell>
        <Cell
          v-if="showSubjectName"
          size="2"
          :column-header="$str('user_activities:subject_header', 'mod_perform')"
        >
          {{ subjectInstance.subject_user.fullname }}
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

import SubjectInstancesQuery from 'mod_perform/graphql/subject_instances.graphql';
import { notify } from 'totara_core/notifications';

const ABOUT_SELF = 'self';
const ABOUT_OTHERS = 'others';

const TOAST_DURATION = 10 * 1000; // in microseconds.

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
    /**
     * Show a generic success toast.
     */
    showSuccessNotification() {
      notify({
        duration: TOAST_DURATION,
        message: this.$str('toast_success_save_response', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: TOAST_DURATION,
        message: this.$str('toast_error_save_response', 'mod_perform'),
        type: 'error',
      });
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
      "user_activities:title_header",
      "toast_success_save_response",
      "toast_error_save_response"
    ]
  }
</lang-strings>
