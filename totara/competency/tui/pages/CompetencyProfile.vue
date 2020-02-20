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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_competency
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
        <div v-if="!noAssignments">
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
              {{ $str('header:competencies', 'totara_competency') }}
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
                    :label="$str('charts', 'totara_competency')"
                  >
                    <BarChartIcon />
                  </ToggleButtonIcon>
                  <ToggleButtonIcon
                    value="table"
                    :label="$str('table', 'totara_competency')"
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
import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';
import BarChartIcon from 'totara_core/components/icons/common/BarChart';
import ListIcon from 'totara_core/components/icons/common/List';
import Responsive from 'totara_core/components/responsive/Responsive';
import Loader from 'totara_core/components/loader/Loader';
import ActionLink from 'totara_core/components/links/ActionLink';
import ProgressAssignmentFilters from 'totara_competency/components/ProgressAssignmentFilters';
import CompetencyList from 'totara_competency/components/profile/competency_list/List';
import CompetencyCharts from 'totara_competency/components/profile/CompetencyCharts';
import NoCompetencyAssignments from 'totara_competency/components/profile/NoCompetencyAssignments';
import CurrentProgress from 'totara_competency/components/profile/CurrentProgress';
import UserHeader from 'totara_competency/components/UserHeader';
import ProgressQuery from 'totara_competency/graphql/progress_for_user';
import { pick, groupBy } from 'totara_core/util';

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
        if (!this.currentProgressData.length) {
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
    "charts",
    "table",
    "assign_competencies",
    "self_assign_competencies",
    "latest_achievement",
    "current_assignment_progress",
    "header:competencies"
  ],
  "pathway_manual": [
    "rate_competencies"
  ]
}
</lang-strings>
