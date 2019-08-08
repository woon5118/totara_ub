<template>
  <div>
    <Preloader :display="$apollo.loading" />
    <hr />
    <Filters
      :is-for-archived="!filterNotArchived"
      :default-order="order"
      :default-filter-values="{
        proficient: proficientFilter,
        search: searchFilter,
      }"
      @filters-updated="filtersUpdated"
      @order-updated="orderUpdated"
    />
    <hr />
    <CurrentList
      v-if="filterNotArchived"
      :competencies="competencies"
      :scales="scales"
    />
    <ArchivedList v-else :competencies="competencies" :scales="scales" />
  </div>
</template>

<script>
import CompetencyProgressQuery from '../../../../webapi/ajax/competency_progress_for_user.graphql';
import CompetencyScalesQuery from '../../../../webapi/ajax/scales.graphql';
import Filters from './Filters';
import Preloader from '../../Preloader';
import ArchivedList from './ArchivedList';
import CurrentList from './CurrentList';

export default {
  components: {
    CurrentList,
    ArchivedList,
    Preloader,
    Filters,
  },

  props: {
    filters: {
      required: false,
      type: Object,
      default: () => {},
    },
    userId: {
      required: true,
      type: Number,
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

  data() {
    return {
      competencies: [],
      order: 'alphabetical',
      proficientFilter: null,
      searchFilter: '',
      scales: [],
    };
  },

  computed: {
    selectedFilters() {
      // We gotta conditionally add proficient filter, since apparently it's not designed to be used for
      // archived assignments

      const extraFilters = {
        search: this.searchFilter,
      };

      if (this.filterNotArchived) {
        extraFilters.proficient = this.proficientFilter;
      }

      return Object.assign(extraFilters, this.filters);
    },

    filterNotArchived() {
      return this.filters && this.filters.status !== 2;
    },
  },

  apollo: {
    competencies: {
      query: CompetencyProgressQuery,
      variables() {
        if (this.filterNotArchived) {
          if (this.order === 'recently-archived') {
            this.order = 'alphabetical';
          }
        } else {
          if (this.order === 'recently-assigned') {
            this.order = 'alphabetical';
          }
        }

        return {
          user_id: this.userId,
          order: this.order,
          filters: this.selectedFilters,
        };
      },
      update({ totara_competency_profile_competency_progress: data }) {
        return data;
      },
    },

    scales: {
      query: CompetencyScalesQuery,
      variables() {
        return {
          competency_id: this.competencies.map(
            ({ competency }) => competency.id
          ),
        };
      },
      update({ totara_competency_scales: data }) {
        return data;
      },

      skip() {
        return !this.competencies.length;
      },
    },
  },

  methods: {
    filtersUpdated({ search, proficient }) {
      this.searchFilter = search;
      this.proficientFilter = proficient;
    },
    orderUpdated(order) {
      this.order = order;
    },

    competencyDetailsLink(row) {
      let link = `${this.baseUrl}/details/?competency_id=${row.competency.id}`;

      if (!this.isMine) {
        link += `&user_id=${this.userId}`;
      }

      return link;
    },
  },
};
</script>
