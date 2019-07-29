<template>
  <div>
    <transition name="totara_competency-profile__transition-fade">
      <div v-if="isLoading" class="totara_competency-profile__overlay">
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
      <div
        class="totara_competency-profile__no-assignments-search-competencies"
      >
        <a
          :href="selfAssignmentUrl"
          class="btn totara_style-btn"
          v-text="$str('search_competencies', 'totara_competency')"
        ></a>
      </div>
    </div>
    <div v-else>
      <!-- Header-->
      <div class="totara_competency-profile__header">
        <ul>
          <li v-for="item in progress" :key="item.key">
            <AssignmentProgress :progress="item"> </AssignmentProgress>
          </li>
        </ul>
        <div class="totara_competency-profile__header-user-details">
          <img :src="profilePicture" :alt="userName" />
          <a
            :href="selfAssignmentUrl"
            class="btn totara_style-btn"
            v-text="$str('search_competencies', 'totara_competency')"
          ></a>
          <div
            v-if="data.latest_achievement"
            class="totara_competency-profile__latest-achievement"
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
        <div class="totara_competency-profile__filters_bar">
          <div class="totara_competency-profile__tabs">
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
        <transition name="totara_competency-profile__transition-fade">
          <div v-if="activeTab === 'charts'">
            <!-- Available charts -->
            <CompetencyCharts :data="data"></CompetencyCharts>
          </div>
        </transition>
        <transition name="totara_competency-profile__transition-fade">
          <div v-if="activeTab === 'table'">
            <!-- List of assignments -->
            <CompetencyList :filters="selectedFilters" :user-id="userId">
            </CompetencyList>
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
      isLoading: true,
      progress: [],
    };
  },

  computed: {
    chartsTabClass: function() {
      return {
        'totara_competency-profile__tab_toggle_link': true,
        active: this.activeTab === 'charts',
      };
    },

    tableTabClass: function() {
      return {
        'totara_competency-profile__tab_toggle_link': true,
        active: this.activeTab === 'table',
      };
    },

    filters: function() {
      return this.data.filters ? this.data.filters : [];
    },

    noAssignments: function() {
      return !this.isLoading && !this.filters.length;
    },
  },

  mounted: function() {
    this.loadProgressData(true);
    this.selectTab('table'); // Let's open
  },

  methods: {
    selectTab: function(tab) {
      if (this.activeTab === tab) {
        return;
      }

      this.activeTab = tab;
    },

    loadProgressData: function(initial) {
      if (typeof initial === 'undefined') {
        initial = false;
      }

      this.isLoading = true;

      let args = {
        user_id: this.userId,
        filters: this.selectedFilters,
      };

      this.$webapi
        .query('totara_competency_progress_for_user', args)
        .then(
          function(data) {
            data = data['totara_competency_profile_progress'];
            this.data = data;

            if (initial) {
              this.setProgress(data);
            }

            this.progressDataLoaded(data, args.filters);

            this.isLoading = false;
          }.bind(this)
        )
        .catch(
          function(error) {
            this.isLoading = false;
            console.error(error);
          }.bind(this)
        );
    },

    progressDataLoaded: function(data, filters) {
      if (filters.status === ARCHIVED_ASSIGNMENT) {
        this.selectTab('table');
      }
    },

    setProgress(data) {
      this.progress = [];

      data.items.forEach(
        function(item) {
          this.progress.push({
            key: item.key,
            name: item.name,
            overall_progress: item.overall_progress,
          });
        }.bind(this)
      );
    },
  },
};
</script>

<style lang="scss">
// Header, the big boy.
.totara_competency-profile__header {
  display: flex;

  flex-direction: column;

  @media (min-width: $totara_style-screen_sm_min) {
    flex-direction: row;
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

  & .totara_competency-profile__header-user-details {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-self: start;
    max-width: 300px;

    & > :not(:last-child) {
      margin-bottom: 15px;
    }

    & .totara_competency-profile__latest-achievement {
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

// Tabs toggle links

.totara_competency-profile__tab_toggle_link {
  &:not(.active) {
    color: #287b7c;
    cursor: pointer;
  }

  &.active {
    color: #005ebd;
  }
}

// Main filters bar

.totara_competency-profile__filters_bar {
  display: flex;
  flex-direction: row-reverse;
  line-height: 32px;

  & > div {
    &:not(:first-child) {
      margin-right: 15px;
    }
  }
}

// Tabs

.totara_competency-profile__tabs {
  display: inline-flex;
}

// Loading overlay

.totara_competency-profile__overlay {
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

.totara_competency-profile__no-assignments-search-competencies {
  display: flex;
  flex-direction: row-reverse;
  margin-top: 35px;
}

// Fade transitions

.totara_competency-profile__transition-fade-enter-active,
.totara_competency-profile__transition-fade-leave-active {
  transition: opacity 0.5s;
}
.totara_competency-profile__transition-fade-enter,
.totara_competency-profile__transition-fade-leave-to {
  opacity: 0;
}
</style>
<lang-strings>
    {
       "totara_competency": ["latest_achievement", "search_competencies", "no_competencies_assigned", "loading"]
    }
</lang-strings>
