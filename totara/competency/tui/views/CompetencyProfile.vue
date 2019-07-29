<template>
  <div>
    <transition name="tui-CompetencyProfile__transition-fade">
      <div v-if="isLoading" class="tui-CompetencyProfile__overlay">
        <div>
          <h1
            class="title is-1"
            v-text="$str('loading', 'totara_competency')"
          ></h1>
        </div>
      </div>
    </transition>
    <div v-if="noAssignments">
      <div class="alert alert-info alert-with-icon">
        <!-- TODO bootstrap alert -->
        <div class="alert-icon">
          <FlexIcon id="notification-info"></FlexIcon>
        </div>
        <div
          class="alert-message"
          v-text="$str('no_competencies_assigned', 'totara_competency')"
        ></div>
      </div>
      <div class="tui-CompetencyProfile__no-assignments-search-competencies">
        <a
          :href="selfAssignmentUrl"
          class="btn totara_style-btn"
          v-text="$str('search_competencies', 'totara_competency')"
        ></a>
      </div>
    </div>
    <div v-else>
      <!-- Header-->
      <div class="tui-CompetencyProfile__header">
        <ul>
          <li v-for="(item, key) in progress" :key="key">
            <AssignmentProgress :progress="item"></AssignmentProgress>
          </li>
        </ul>
        <div class="tui-CompetencyProfile__header-user-details">
          <img :src="profilePicture" :alt="userName" />
          <a
            :href="selfAssignmentUrl"
            class="btn totara_style-btn"
            v-text="$str('search_competencies', 'totara_competency')"
          ></a>
          <div
            v-if="data.latest_achievement"
            class="tui-CompetencyProfile__latest-achievement"
          >
            <div>
              <div>
                <FlexIcon
                  id="star"
                  :alt="$str('latest_achievement', 'totara_competency')"
                />
              </div>
              <strong
                v-text="$str('latest_achievement', 'totara_competency')"
              ></strong>
            </div>
            <span v-text="data.latest_achievement"></span>
          </div>
        </div>
      </div>
      <div>
        <hr />
        <div class="tui-CompetencyProfile__filters-bar">
          <div class="tui-CompetencyProfile__tabs">
            <div :class="chartsTabClass" @click="selectTab('charts')">
              <FlexIcon id="bar-chart" size="500" />
            </div>
            <div :class="tableTabClass" @click="selectTab('table')">
              <FlexIcon id="bars" size="500" />
            </div>
          </div>
          <ProgressAssignmentFilters
            v-model="selectedFilters"
            :filters="filters"
            @changed="loadProgressData()"
          ></ProgressAssignmentFilters>
        </div>
        <transition name="tui-CompetencyProfile__transition-fade">
          <div v-if="activeTab === 'charts'">
            <!-- Available charts -->
            <CompetencyCharts :data="data"></CompetencyCharts>
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
            ></CompetencyList>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import AssignmentProgress from '../container/AssignmentProgress';
import FlexIcon from 'totara_core/presentation/icons/FlexIcon';
import ProgressAssignmentFilters from '../presentation/ProgressAssignmentFilters';
import CompetencyList from '../presentation/Profile/CompetencyList';
import CompetencyCharts from '../presentation/Profile/CompetencyCharts';

const ACTIVE_ASSIGNMENT = 1;
const ARCHIVED_ASSIGNMENT = 2;

export default {
  components: {
    CompetencyCharts,
    CompetencyList,
    ProgressAssignmentFilters,
    FlexIcon,
    AssignmentProgress
  },
  props: {
    profilePicture: {
      required: true,
      type: String
    },
    selfAssignmentUrl: {
      required: true,
      type: String
    },
    userId: {
      required: true,
      type: Number
    },
    userName: {
      required: true,
      type: String
    },
    baseUrl: {
      required: true,
      type: String
    },
    isMine: {
      required: true,
      type: Boolean
    }
  },

  data: function() {
    return {
      data: {},
      activeTab: 'charts',
      selectedFilters: {
        status: ACTIVE_ASSIGNMENT,
        type: null,
        user_group_id: null,
        user_group_type: null
      },
      isLoading: true,
      progress: []
    };
  },

  computed: {
    chartsTabClass() {
      return {
        'tui-CompetencyProfile__tab-toggle-link': true,
        active: this.activeTab === 'charts'
      };
    },

    tableTabClass() {
      return {
        'tui-CompetencyProfile__tab-toggle-link': true,
        active: this.activeTab === 'table'
      };
    },

    filters() {
      return this.data.filters ? this.data.filters : [];
    },

    noAssignments() {
      return !this.isLoading && !this.filters.length;
    }
  },

  mounted() {
    this.loadProgressData(true);
    this.selectTab('table'); // Let's open
  },

  methods: {
    selectTab(tab) {
      if (this.activeTab === tab) {
        return;
      }

      this.activeTab = tab;
    },

    loadProgressData(initial = false) {
      this.isLoading = true;

      let args = {
        user_id: this.userId,
        filters: this.selectedFilters
      };

      this.$webapi
        .query('totara_competency_progress_for_user', args)
        .then(({ totara_competency_profile_progress: progress }) => {
          this.data = progress;

          if (initial) {
            this.setProgress(progress);
          }

          this.progressDataLoaded(progress, args.filters);

          this.isLoading = false;
        })
        .catch(error => {
          this.isLoading = false;
          console.error(error);
        });
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
          overall_progress: overall_progress
        });
      });
    }
  }
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
       "totara_competency": ["latest_achievement", "search_competencies", "no_competencies_assigned", "loading"]
    }
</lang-strings>
