<template>
  <div>
    <h2 v-text="$str('competency_profile', 'totara_competency')" />
    <Preloader :display="$apollo.loading" />
    <NoCompetencyAssignments
      v-if="noAssignments"
      :self-assignment-url="selfAssignmentUrl"
    />
    <div v-else>
      <!-- Header-->
      <ProfileHeader
        :is-mine="isMine"
        :self-assignment-url="selfAssignmentUrl"
        :user-name="userName"
        :data="currentProgressData"
        :profile-picture="profilePicture"
        :latest-achievement="data.latest_achievement"
      />
      <div>
        <hr />
        <div class="tui-CompetencyProfile__filters-bar">
          <div class="tui-CompetencyProfile__tabs">
            <div :class="chartsTabClass" @click="selectTab('charts')">
              <FlexIcon icon="bar-chart" size="500" />
            </div>
            <div :class="tableTabClass" @click="selectTab('table')">
              <FlexIcon icon="bars" size="500" />
            </div>
          </div>
          <ProgressAssignmentFilters
            v-model="selectedFilters"
            :filters="filters"
          />
        </div>
        <transition name="tui-CompetencyProfile__transition-fade">
          <div v-if="activeTab === 'charts'">
            <!-- Available charts -->
            <CompetencyCharts :data="data" />
          </div>
        </transition>
        <transition name="tui-CompetencyProfile__transition-fade">
          <div v-if="activeTab === 'table'">
            <!-- List of assignments -->
            <CompetencyList
              :filters="selectedFilters"
              :user-id="userId"
              :is-mine="isMine"
              :base-url="baseUrl"
            />
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import ProgressAssignmentFilters from '../presentation/ProgressAssignmentFilters';
import CompetencyList from '../presentation/Profile/CompetencyList/List';
import CompetencyCharts from '../presentation/Profile/CompetencyCharts';

import ProgressQuery from '../../webapi/ajax/progress_for_user.graphql';
import Preloader from '../presentation/Preloader';
import NoCompetencyAssignments from '../presentation/Profile/NoCompetencyAssignments';
import ProfileHeader from '../presentation/Profile/Header';

const ACTIVE_ASSIGNMENT = 1;
const ARCHIVED_ASSIGNMENT = 2;

export default {
  components: {
    ProfileHeader,
    NoCompetencyAssignments,
    Preloader,
    CompetencyCharts,
    CompetencyList,
    ProgressAssignmentFilters,
    FlexIcon,
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
  },

  data: function() {
    return {
      data: {},
      activeTab: 'charts',
      selectedFilters: {
        status: ACTIVE_ASSIGNMENT,
        type: null,
        user_group_id: null,
        user_group_type: null,
      },
      currentProgressData: [],
    };
  },

  computed: {
    chartsTabClass() {
      return {
        'tui-CompetencyProfile__tab-toggle-link': true,
        active: this.activeTab === 'charts',
      };
    },

    tableTabClass() {
      return {
        'tui-CompetencyProfile__tab-toggle-link': true,
        active: this.activeTab === 'table',
      };
    },

    filters() {
      if (!this.data) {
        return [];
      }

      return this.data.filters ? this.data.filters : [];
    },

    noAssignments() {
      return !this.$apollo.loading && !this.filters.length;
    },
  },

  apollo: {
    data: {
      query: ProgressQuery,
      variables() {
        return {
          user_id: this.userId,
          filters: this.selectedFilters,
        };
      },
      update({ totara_competency_profile_progress: data }) {
        return data;
      },

      result({ data: { totara_competency_profile_progress: data } }) {
        if (!this.currentProgressData.length) {
          this.currentProgressData = data.items.slice(0);
        }

        this.progressDataLoaded(this.selectedFilters);
      },
    },
  },

  methods: {
    selectTab(tab) {
      if (this.activeTab === tab) {
        return;
      }

      this.activeTab = tab;
    },

    progressDataLoaded(filters) {
      if (filters.status === ARCHIVED_ASSIGNMENT) {
        this.selectTab('table');
      }
    },
  },
};
</script>

<style lang="scss">
.tui-CompetencyProfile__ {
  &tabs {
    display: inline-flex;
  }

  // Tabs toggle links
  &tab-toggle-link {
    &:not(.active) {
      color: #287b7c;
      cursor: pointer;
    }

    &.active {
      color: #005ebd;
    }
  }

  // Main filters bar
  &filters-bar {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-between;
    margin-bottom: 1rem;
    line-height: 32px;
  }
}
</style>
<lang-strings>
    {
       "totara_competency": ["latest_achievement", "assign_competencies", "no_competencies_assigned", "loading", "competency_profile"]
    }
</lang-strings>
