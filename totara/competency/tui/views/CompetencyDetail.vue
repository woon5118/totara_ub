<template>
  <div>
    <div class="tui-CompetencyDetail__nav-links">
      <div class="tui-CompetencyDetail__nav-links-left">
        <a :href="goBackLink">{{
          $str('back_to_competency_profile', 'totara_competency')
        }}</a>
      </div>
      <div class="tui-CompetencyDetail__nav-links-right">
        <ul>
          <li v-text="$str('viewing')"></li>
          <li>
            <a
              href="#"
              class="btn btn-sm"
              :class="tabClass(activeTab === 'details')"
              @click.prevent="activeTab = 'details'"
              v-text="$str('overview', 'totara_competency')"
            ></a>
          </li>
          <li>
            <a
              href="#"
              class="btn btn-sm"
              :class="tabClass(activeTab === 'log')"
              @click.prevent="activeTab = 'log'"
              v-text="$str('activity_log', 'totara_competency')"
            ></a>
          </li>
        </ul>
      </div>
    </div>
    <!-- Tabs to toggle -->
    <div>
      <!-- Competency details -->
      <div v-if="activeTab === 'details'">
        <Details :competency-id="competencyId" :user-id="userId"></Details>
      </div>
      <!-- Competency activity log -->
      <div v-if="activeTab === 'log'">
        <ActivityLog
          :competency-id="competencyId"
          :user-id="userId"
        ></ActivityLog>
      </div>
    </div>
  </div>
</template>

<script>
import Details from '../presentation/Details/Details';
import ActivityLog from '../presentation/Details/ActivityLog';

export default {
  components: { ActivityLog, Details },
  props: {
    goBackLink: {
      required: true,
      type: String
    },
    userId: {
      required: true,
      type: Number
    },
    competencyId: {
      required: true,
      type: Number
    }
  },

  data() {
    return {
      activeTab: 'details'
    };
  },

  computed: {},

  mounted: function() {
    // Load the list via GraphQL
  },

  methods: {
    tabClass(isActive) {
      return [isActive ? 'btn-primary' : 'btn-secondary'];
    }
  }
};
</script>
<style lang="scss">
.tui-CompetencyDetail__ {
  &nav-links {
    display: flex;
    flex-direction: row;
    justify-content: space-between;

    &-left {
      align-self: start;
    }

    &-right {
      align-self: end;

      & > ul {
        display: flex;

        padding: 0;
        margin: 0;
        list-style: none;
        flex-grow: 1;
        flex-wrap: wrap;
        line-height: 34px;

        & > li:not(:last-child) {
          margin-right: 15px;
        }
      }
    }
  }
}
</style>

<lang-strings>
    {
        "totara_hierarchy": [
            "assign"
        ],
        "totara_competency": [
            "activity_log",
            "back_to_competency_profile",
            "overview",
            "search_competencies",
            "search_competencies_descriptive",
            "unassigned"
        ],
        "moodle": [
          "viewing"
        ]
    }
</lang-strings>
