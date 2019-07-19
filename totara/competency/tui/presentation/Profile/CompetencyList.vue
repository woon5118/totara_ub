<template>
  <div>
    <hr />
    <!-- Filters bar -->
    <div
      style="display: flex; flex-direction: row; justify-content: space-between"
    >
      <div style="justify-content: left; display: flex; flex-direction: row">
        <FlexIcon id="filter" size="500" alt="Filters"></FlexIcon>
        <template v-if="true">
          <label for="competency-profile-proficient-filter">
            Proficient status
          </label>
          <select
            id="competency-profile-proficient-filter"
            v-model="proficientFilter"
          >
            <option :value="null">Any</option>
            <option :value="true">Proficient</option>
            <option :value="false">Not proficient</option>
          </select>
        </template>
        <label class="sr-only" for="competency-profile-proficient-text-search">
          Ugly search text input
        </label>
        <input
          id="competency-profile-proficient-text-search"
          v-model.lazy="searchFilter"
          class="input"
          type="text"
          placeholder="Search"
        />
      </div>
      <div style="justify-content: right; display: flex; flex-direction: row">
        <label for="competency-profile-order-assignments-by">
          Sort by
        </label>
        <select id="competency-profile-order-assignments-by" v-model="order">
          <option value="alphabetical">Alphabetical</option>
          <option value="recently-assigned">Recently assigned</option>
          <option value="recently-archived">Recently archived</option>
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
    size: 'lg',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'sm',
    alignment: 'center',
  },
  {
    key: 'rating',
    title: 'Rating',
    size: 'xs',
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
</style>
<lang-strings>
    {
    "totara_competency": ["latest_achievement", "search_competencies", "no_competencies_assigned", "loading"]
    }
</lang-strings>
