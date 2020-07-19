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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module totara_competency
-->

<template>
  <div class="tui-competencyProfile">
    <Loader :loading="$apollo.loading">
      <UserHeader
        v-if="!isMine"
        :user-name="userName"
        :profile-picture="profilePicture"
      />
      <div
        class="tui-competencyProfile__split tui-competencyProfile__titleSection"
      >
        <h2 class="tui-competencyProfile__title">
          {{ $str('competency_profile', 'totara_competency') }}
        </h2>
        <div v-if="!noAssignments" class="tui-competencyProfile__buttons">
          <ActionLink
            v-if="rateCompetenciesUrl"
            :href="rateCompetenciesUrl"
            :text="$str('rate_competencies', 'pathway_manual')"
          />
          <ActionLink
            :href="selfAssignmentUrl"
            :text="
              isMine
                ? $str('self_assign_competencies', 'totara_competency')
                : $str('assign_competencies', 'totara_competency')
            "
          />
        </div>
      </div>
      <NoCompetencyAssignments
        v-if="noAssignments"
        :is-mine="isMine"
        :self-assignment-url="selfAssignmentUrl"
      />
      <div v-else>
        <h3 class="tui-competencyProfile__sectionTitle">
          {{ $str('current_assignment_progress', 'totara_competency') }}
        </h3>
        <CurrentProgress
          :data="currentProgressData"
          :latest-achievement="data.latest_achievement"
          :is-current-user="isMine"
        />
        <Responsive
          v-slot="{ currentBoundaryName }"
          :breakpoints="[
            { name: 'small', boundaries: [0, 700] },
            { name: null, boundaries: [701, 701] },
          ]"
        >
          <div>
            <h3 class="tui-competencyProfile__sectionTitle">
              {{ $str('header_competencies', 'totara_competency') }}
            </h3>
            <div
              class="tui-competencyProfile__split tui-competencyProfile__filtersBar"
            >
              <ProgressAssignmentFilters
                v-model="selectedFilters"
                :filters="filterOptions"
              />
              <div
                v-if="currentBoundaryName != 'small'"
                class="tui-competencyProfile__tabs"
              >
                <ToggleSet v-model="activeTab">
                  <ToggleButtonIcon
                    value="charts"
                    :label="$str('toggle_charts', 'totara_competency')"
                  >
                    <BarChartIcon />
                  </ToggleButtonIcon>
                  <ToggleButtonIcon
                    value="table"
                    :label="$str('toggle_table', 'totara_competency')"
                  >
                    <ListIcon />
                  </ToggleButtonIcon>
                </ToggleSet>
              </div>
            </div>
            <div
              v-if="currentBoundaryName != 'small' && activeTab === 'charts'"
            >
              <CompetencyCharts
                :data="data"
                :user-id="userId"
                :is-current-user="isMine"
              />
            </div>
            <div v-if="currentBoundaryName == 'small' || activeTab === 'table'">
              <CompetencyList
                :filters="selectedFilters"
                :user-id="userId"
                :is-mine="isMine"
                :base-url="baseUrl"
              />
            </div>
          </div>
        </Responsive>
      </div>
    </Loader>
  </div>
</template>

<script>
import ToggleSet from 'tui/components/buttons/ToggleSet';
import ToggleButtonIcon from 'tui/components/buttons/ToggleButtonIcon';
import BarChartIcon from 'tui/components/icons/common/BarChart';
import ListIcon from 'tui/components/icons/common/List';
import Responsive from 'tui/components/responsive/Responsive';
import Loader from 'tui/components/loader/Loader';
import ActionLink from 'tui/components/links/ActionLink';
import ProgressAssignmentFilters from 'totara_competency/components/ProgressAssignmentFilters';
import CompetencyList from 'totara_competency/components/profile/competency_list/List';
import CompetencyCharts from 'totara_competency/components/profile/CompetencyCharts';
import NoCompetencyAssignments from 'totara_competency/components/profile/NoCompetencyAssignments';
import CurrentProgress from 'totara_competency/components/profile/CurrentProgress';
import UserHeader from 'totara_competency/components/UserHeader';
import ProgressQuery from 'totara_competency/graphql/progress_for_user';
import { pick, groupBy } from 'tui/util';

const ACTIVE_ASSIGNMENT = 1;
// const ARCHIVED_ASSIGNMENT = 2;

export default {
  components: {
    ToggleSet,
    ToggleButtonIcon,
    BarChartIcon,
    ListIcon,
    Responsive,
    Loader,
    ActionLink,
    CurrentProgress,
    UserHeader,
    NoCompetencyAssignments,
    CompetencyCharts,
    CompetencyList,
    ProgressAssignmentFilters,
  },

  props: {
    profilePicture: {
      required: true,
      type: String,
    },
    selfAssignmentUrl: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
    userName: {
      required: true,
      type: String,
    },
    baseUrl: {
      required: true,
      type: String,
    },
    isMine: {
      required: true,
      type: Boolean,
    },
    canRateCompetencies: {
      required: true,
      type: Boolean,
    },
  },

  data() {
    return {
      data: {
        latest_achievement: null,
      },
      activeTab: 'charts',
      selectedFilters: this.$_addFilterKey({
        status: ACTIVE_ASSIGNMENT,
        type: null,
        user_group_id: null,
        user_group_type: null,
      }),
      currentProgressData: [],
    };
  },

  apollo: {
    data: {
      query: ProgressQuery,
      variables() {
        return {
          user_id: this.userId,
          filters: pick(this.selectedFilters, [
            'status',
            'type',
            'user_group_id',
            'user_group_type',
          ]),
        };
      },
      update: data => data.totara_competency_profile_progress,
      result({ data: { totara_competency_profile_progress: data } }) {
        // show current progress only when there is active assignment
        if (
          this.selectedFilters.status == ACTIVE_ASSIGNMENT &&
          !this.currentProgressData.length
        ) {
          this.currentProgressData = data.items.slice(0);
        }

        // ensure the filter we requested with is actually allowed
        if (data.filters && data.filters.length) {
          const filter = this.selectedFilters;
          const isStatusFilter =
            filter.type === null &&
            filter.user_group_id === null &&
            filter.user_group_type === null;

          const filterPresent = isStatusFilter
            ? data.filters.some(x => x.status == this.selectedFilters.status)
            : data.filters.some(
                x => this.$_filterKey(x) == this.selectedFilters.key
              );

          if (!filterPresent) {
            // we requested with an invalid filter, try again with a valid one
            this.selectedFilters = this.$_addFilterKey(
              isStatusFilter
                ? {
                    status: data.filters[0].status,
                    type: null,
                    user_group_id: null,
                    user_group_type: null,
                  }
                : data.filters[0]
            );
          }
        }
      },
    },
  },

  computed: {
    /**
     * Get options for the filter.
     *
     * @returns {Array<{id: String, name: String, value: Object, filters: Array<{id: String, name: String, value: Object}>}>}
     */
    filterOptions() {
      if (!this.data || !this.data.filters) {
        return [];
      }

      return Object.entries(
        groupBy(this.data.filters, filter => filter.status)
        // eslint-disable-next-line no-unused-vars
      ).map(([key, filters]) => {
        const groupValue = this.$_addFilterKey({
          status: filters[0].status,
          type: null,
          user_group_id: null,
          user_group_type: null,
        });
        return {
          id: groupValue.key,
          name: filters[0].status_name,
          value: groupValue,
          filters: filters.map(filter => {
            const value = this.$_addFilterKey(this.$_filterValue(filter));
            return {
              id: value.key,
              name: filter.name,
              value,
            };
          }),
        };
      });
    },

    /**
     * True if we're not loading and there are no filter options.
     */
    noAssignments() {
      return !this.$apollo.loading && !this.filterOptions.length;
    },

    rateCompetenciesUrl() {
      if (!this.canRateCompetencies) {
        return null;
      }

      return this.$url('/totara/competency/rate_competencies.php', {
        user_id: this.userId,
      });
    },
  },

  methods: {
    /**
     * Filter a filter value down to the properties that the query needs
     *
     * @param {object} obj
     * @returns {object}
     */
    $_filterValue(obj) {
      return pick(obj, ['status', 'type', 'user_group_id', 'user_group_type']);
    },

    /**
     * Add the filter key to a copy of the specified object and return it
     *
     * @param {object} obj
     * @returns {object}
     */
    $_addFilterKey(obj) {
      return Object.assign({ key: this.$_filterKey(obj) }, obj);
    },

    /**
     * Generate a key for a particular filter
     *
     * @param {object} obj
     * @returns {object}
     */
    $_filterKey(obj) {
      return JSON.stringify([
        obj.status,
        obj.type,
        obj.user_group_id,
        obj.user_group_type,
      ]);
    },
  },
};
</script>

<lang-strings>
{
  "totara_competency": [
    "competency_profile",
    "toggle_charts",
    "toggle_table",
    "assign_competencies",
    "self_assign_competencies",
    "latest_achievement",
    "current_assignment_progress",
    "header_competencies"
  ],
  "pathway_manual": [
    "rate_competencies"
  ]
}
</lang-strings>
