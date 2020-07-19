<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module totara_competency
-->

<template>
  <div>
    <Loader :loading="$apollo.loading">
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
      <CurrentList
        v-if="filterNotArchived"
        :competencies="competencies"
        :base-url="baseUrl"
        :user-id="userId"
        :scales="scales"
      />
      <ArchivedList
        v-else
        :competencies="competencies"
        :scales="scales"
        :base-url="baseUrl"
        :user-id="userId"
      />
    </Loader>
  </div>
</template>

<script>
import Loader from 'totara_core/components/loader/Loader';
import CompetencyProgressQuery from 'totara_competency/graphql/competency_progress_for_user';
import CompetencyScalesQuery from 'totara_competency/graphql/scales';
import Filters from 'totara_competency/components/profile/competency_list/Filters';
import ArchivedList from 'totara_competency/components/profile/competency_list/ArchivedList';
import CurrentList from 'totara_competency/components/profile/competency_list/CurrentList';
import { pick } from 'totara_core/util';

export default {
  components: {
    Loader,
    CurrentList,
    ArchivedList,
    Filters,
  },

  props: {
    filters: {
      required: false,
      type: Object,
      default: () => ({}),
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

      return Object.assign(
        extraFilters,
        pick(this.filters, [
          'status',
          'type',
          'user_group_id',
          'user_group_type',
        ])
      );
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
  },
};
</script>
