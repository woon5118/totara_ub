<template>
  <div>
    <transition name="tui-CompetencyProfile__transition-fade">
      <div v-if="$apollo.loading" class="tui-CompetencyProfile__overlay">
        <div>
          <h1
            class="title is-1"
            v-text="$str('loading', 'totara_competency')"
          />
        </div>
      </div>
    </transition>
    <div v-if="noAssignments">
      <div class="alert alert-info alert-with-icon">
        <!-- TODO bootstrap alert -->
        <div class="alert-icon">
          <FlexIcon icon="notification-info" />
        </div>
        <div
          class="alert-message"
          v-text="$str('no_competencies_assigned', 'totara_competency')"
        />
      </div>
      <div class="tui-CompetencyProfile__no-assignments-search-competencies">
        <a
          :href="selfAssignmentUrl"
          class="btn totara_style-btn"
          v-text="$str('assign_competencies', 'totara_competency')"
        />
      </div>
    </div>
    <div v-else>
      <!-- Header-->
      <div class="tui-CompetencyProfile__header">
        <ul>
          <li v-for="(item, key) in progress" :key="key">
            <AssignmentProgress :progress="item" />
          </li>
        </ul>
        <div class="tui-CompetencyProfile__header-user-details">
          <img v-if="!isMine" :src="profilePicture" :alt="userName" />
          <a
            :href="selfAssignmentUrl"
            class="btn totara_style-btn"
            v-text="$str('assign_competencies', 'totara_competency')"
          />
          <div
            v-if="data.latest_achievement"
            class="tui-CompetencyProfile__latest-achievement"
          >
            <div>
              <div>
                <FlexIcon
                  icon="star"
                  :alt="$str('latest_achievement', 'totara_competency')"
                />
              </div>
              <strong
                v-text="$str('latest_achievement', 'totara_competency')"
              />
            </div>
            <span v-text="data.latest_achievement" />
          </div>
        </div>
      </div>
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
import AssignmentProgress from '../container/AssignmentProgress';
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import ProgressAssignmentFilters from '../presentation/ProgressAssignmentFilters';
import CompetencyList from '../presentation/Profile/CompetencyList';
import CompetencyCharts from '../presentation/Profile/CompetencyCharts';

import ProgressQuery from '../../webapi/ajax/progress_for_user.graphql';

const ACTIVE_ASSIGNMENT = 1;
const ARCHIVED_ASSIGNMENT = 2;

export default {
  components: {
    CompetencyCharts,
    CompetencyList,
    ProgressAssignmentFilters,
    FlexIcon,
    AssignmentProgress,
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
      progress: [],
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
      return !this.isLoading && !this.filters.length;
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
      update(data) {
        return data.totara_competency_profile_progress;
      },

      result({ data: { totara_competency_profile_progress: data } }) {
        if (!this.progress.length) {
          this.setProgress(Object.assign({}, data));
        }

        this.progressDataLoaded(this.data, this.selectedFilters);
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

    progressDataLoaded(data, filters) {
      if (filters.status === ARCHIVED_ASSIGNMENT) {
        this.selectTab('table');
      }
    },

    setProgress(data) {
      this.progress = [];

      data.items.forEach(({ name, overall_progress }) => {
        this.progress.push({
          name: name,
          overall_progress: overall_progress,
        });
      });
    },
  },
};
</script>

<style lang="scss">
.tui-CompetencyProfile__ {
  // Header, the big boy.
  &header {
    display: flex;

    flex-direction: column;

    @media (min-width: $totara_style-screen_sm_min) {
      flex-direction: row;
      justify-content: space-between;
    }

    & > ul {
      display: flex;

      @media (min-width: $totara_style-screen_sm_min) {
        max-width: 80%;
      }

      padding: 0;
      margin: 0;
      list-style: none;
      flex-grow: 1;
      flex-wrap: wrap;
    }

    &-user-details {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-self: start;
      max-width: 300px;

      & > :not(:last-child) {
        margin-bottom: 15px;
      }

      & .tui-CompetencyProfile__latest-achievement {
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;

        & > :not(:last-child) {
          margin-bottom: 10px;
        }
      }
    }
  }

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
    line-height: 32px;

    & > div {
      &:not(:first-child) {
        margin-right: 15px;
      }
    }
  }

  // Loading overlay
  &overlay {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.6);
    z-index: 100;
  }

  // No assignments button stub
  &no-assignments-search-competencies {
    display: flex;
    flex-direction: row-reverse;
    margin-top: 35px;
  }

  // Fade transitions
  &transition-fade-enter-active,
  &transition-fade-leave-active {
    transition: opacity 0.5s;
  }

  &transition-fade-enter,
  &transition-fade-leave-to {
    opacity: 0;
  }
}
</style>
<lang-strings>
    {
       "totara_competency": ["latest_achievement", "assign_competencies", "no_competencies_assigned", "loading"]
    }
</lang-strings>
