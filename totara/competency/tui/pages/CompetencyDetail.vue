<template>
  <div>
    <div class="tui-CompetencyDetail">
      <div class="tui-CompetencyDetail__backLink">
        <a :href="goBackLink">{{ goBackText }}</a>
      </div>
      <div class="tui-CompetencyDetail__nav-links">
        <div class="tui-CompetencyDetail__nav-links_buttons">
          <ul>
            <li v-text="$str('viewing')" />
            <li>
              <a
                href="#"
                class="btn btn-sm"
                :class="tabClass(activeTab === 'details')"
                @click.prevent="activeTab = 'details'"
                v-text="$str('overview', 'totara_competency')"
              />
            </li>
            <li>
              <a
                href="#"
                class="btn btn-sm"
                :class="tabClass(activeTab === 'log')"
                @click.prevent="activeTab = 'log'"
                v-text="$str('activity_log', 'totara_competency')"
              />
            </li>
          </ul>
        </div>
        <div class="tui-CompetencyDetail__nav-links_title">
          <h2 v-text="data.competency.fullname" />
        </div>
      </div>
    </div>
    <!-- Tabs to toggle -->
    <div>
      <!-- Competency details -->
      <div v-if="activeTab === 'details'">
        <Details :competency-id="competencyId" :user-id="userId" :data="data" />
      </div>
      <!-- Competency activity log -->
      <div v-if="activeTab === 'log'">
        <ActivityLog :competency-id="competencyId" :user-id="userId" />
      </div>
    </div>
  </div>
</template>

<script>
import Details from 'totara_competency/components/details/Details';
import ActivityLog from 'totara_competency/components/activity_log/ActivityLog';

import CompetencyDetailsQuery from '../../webapi/ajax/competency_details.graphql';

export default {
  components: { ActivityLog, Details },
  props: {
    goBackLink: {
      required: true,
      type: String,
    },
    goBackText: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
    startingTab: {
      required: false,
      type: String,
      default: 'details',
    },
  },

  data() {
    return {
      activeTab: this.startingTab,
      data: {
        competency: {
          fullname: '',
        },
        items: [],
      },
    };
  },

  computed: {},

  mounted: function() {
    // Load the list via GraphQL
  },

  methods: {
    tabClass(isActive) {
      return [isActive ? 'btn-primary' : 'btn-secondary'];
    },
  },

  apollo: {
    data: {
      query: CompetencyDetailsQuery,
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
};
</script>
<style lang="scss">
.tui-CompetencyDetail__ {
  &backLink {
    display: inline-block;
    align-self: start;
    padding-bottom: var(--tui-gap-2);
  }
  &nav-links {
    &_title {
      margin: 0;
      h2 {
        margin: 0;
      }
    }
    &_buttons {
      align-self: end;

      & > ul {
        display: flex;
        flex-grow: 1;
        flex-wrap: wrap;
        margin: 0;

        padding: 0;
        line-height: var(--tui-font-size-32);
        list-style: none;

        & > li:not(:last-child) {
          margin-right: var(--tui-font-size-14);
        }
      }
    }
    @media (min-width: $tui-screen-sm) {
      display: flex;
      flex-direction: row-reverse;
      &_buttons {
        margin-left: auto;
      }
    }
    @media (max-width: $tui-screen-sm) {
      &_buttons {
        padding-bottom: var(--tui-gap-4);
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
            "assign_competencies",
            "search_competencies_descriptive",
            "unassigned"
        ],
        "moodle": [
          "viewing"
        ]
    }
</lang-strings>
