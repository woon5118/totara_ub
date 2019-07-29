<template>
  <div>
    <hr />
    <!-- Filters bar -->
    <div
      style="display: flex; flex-direction: row; justify-content: space-between;"
    >
      <div
        style="justify-content: left; display: flex; flex-direction: row;"
        class="totara_competency-profile__comp-list-left-filters"
      >
        <FlexIcon id="filter" size="500" alt="Filters"></FlexIcon>
        <template v-if="true">
          <label
            for="competency-profile-proficient-filter"
            style="line-height: 34px;"
          >
            Proficient status
          </label>
          <select
            id="competency-profile-proficient-filter"
            v-model="proficientFilter"
          >
            <option :value="null" v-text="$str('any', 'totara_competency')"
              >Any</option
            >
            <option
              :value="true"
              v-text="$str('proficient', 'totara_competency')"
              >Proficient</option
            >
            <option
              :value="false"
              v-text="$str('not_proficient', 'totara_competency')"
            ></option>
          </select>
        </template>
        <label class="sr-only" for="competency-profile-proficient-text-search">
          Ugly search text input
        </label>
        <input
          id="competency-profile-proficient-text-search"
          v-model.lazy="searchFilter"
          class="totara_competency-profile__input"
          type="text"
          :placeholder="$str('search')"
        />
      </div>
      <div
        style="justify-content: right; display: flex; flex-direction: row; line-height: 34px;"
        class="totara_competency-profile__comp-list-left-filters"
      >
        <label for="competency-profile-order-assignments-by">
          Sort by
        </label>
        <select id="competency-profile-order-assignments-by" v-model="order">
          <option
            value="alphabetical"
            v-text="$str('sort:alphabetical', 'totara_competency')"
          ></option>
          <option
            value="recently-assigned"
            v-text="$str('sort:recently_assigned', 'totara_competency')"
          ></option>
          <option
            value="recently-archived"
            v-text="$str('sort:recently_archived', 'totara_competency')"
          ></option>
        </select>
      </div>
    </div>
    <hr />
    <List :columns="competencyColumns" :data="competencies">
      <template v-slot:column-name="props">
        <div v-text="props.row.competency.fullname"></div>
        <div
          v-if="props.row.assignments.length > 1"
          class="totara_competency-profile__competency-assignments-list"
          v-text="displayAssignmentsList(props.row)"
        ></div>
      </template>
      <template v-slot:column-proficient="props">
        <template v-if="props.row.proficient">
          <FlexIcon id="star" alt="//TODO add something here" />
        </template>
      </template>
      <template v-slot:column-rating="props">
        <template v-if="props.row.my_value">
          {{ props.row.my_value.name }}
        </template>
      </template>
    </List>
  </div>
</template>

<script>
import List from '../../container/List';
import FlexIcon from 'totara_core/presentation/icons/FlexIcon';

let competencyColumns = [
  {
    key: 'name',
    value: 'competency.fullname',
    title: 'Competency',
    grow: true,
    size: 'md',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'xs',
    alignment: 'center',
  },
  {
    key: 'rating',
    title: 'Rating',
    size: 'sm',
  },
];

export default {
  components: {
    FlexIcon,
    List,
  },

  props: {
    filters: {
      required: false,
      type: Object,
      default: function() {
        return {};
      },
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      competencies: [],
      isLoading: false,
      order: 'alphabetical',
      proficientFilter: null,
      searchFilter: '',
    };
  },

  computed: {
    competencyColumns: function() {
      return competencyColumns;
    },

    selectedFilters: function() {
      console.log('Prior', this.filters);
      return Object.assign(
        {
          search: this.searchFilter,
          proficient: this.proficientFilter,
        },
        this.filters
      );
    },
  },

  watch: {
    selectedFilters: function() {
      this.loadCompetencies();
    },

    order: function() {
      this.loadCompetencies();
    },
  },

  mounted: function() {
    this.loadCompetencies();
  },

  methods: {
    loadCompetencies: function() {
      this.isLoading = true;

      let args = {
        user_id: this.userId,
        order: this.order,
        filters: this.selectedFilters,
      };

      this.$webapi
        .query('totara_competency_competency_progress_for_user', args)
        .then(
          function(data) {
            data = data['totara_competency_profile_competency_progress'];
            this.competencies = data;

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

    displayAssignmentsList(row) {
      let names = [];

      row.assignments.forEach(
        function(assignment) {
          names.push(this.shortenText(assignment.progress_name, 50));
        }.bind(this)
      );

      return names.join(', ');
    },

    shortenText: function(str, maxLen, separator = ' ') {
      if (str.length <= maxLen) return str;
      return str.substr(0, str.lastIndexOf(separator, maxLen));
    },
  },
};
</script>
<style lang="scss">
.totara_competency-profile__competency-assignments-list {
  font-size: smaller;
  font-weight: lighter;
  font-style: italic;
  color: #a9a9a9;
}

.totara_competency-profile__input {
  border: 1px #ccc solid;
  border-radius: 4px;
  padding: 6px 12px;
  transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  height: 34px;
}

.totara_competency-profile__comp-list-left-filters {
  > *:not(:last-child) {
    margin-right: 1rem;
  }
}
</style>
<lang-strings>
    {
      "totara_competency": ["proficient", "not_proficient", "any", "sort:alphabetical", "sort:recently_archived", "sort:recently_assigned"],
      "moodle": ["search"]
    }
</lang-strings>
