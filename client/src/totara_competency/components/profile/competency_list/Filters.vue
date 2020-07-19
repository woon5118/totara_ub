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
  <!-- Filters bar -->
  <FilterBar
    v-model="filters"
    class="tui-competencyProfileListFilters"
    :title="this.$str('filter_competencies', 'totara_competency')"
  >
    <template v-slot:filters-left="{ stacked }">
      <SelectFilter
        v-if="!isForArchived"
        v-model="filters.proficient"
        :label="$str('proficiency_status', 'totara_competency')"
        :show-label="true"
        :options="proficientOptions"
        :stacked="stacked"
        @input="filtersUpdated"
      />
      <SearchFilter
        v-model="filters.search"
        :label="$str('search')"
        :stacked="stacked"
        @input="filtersUpdatedDebounced"
      />
    </template>
    <template v-slot:filters-right="{ stacked }">
      <SelectFilter
        v-model="order"
        :label="$str('sortby')"
        :show-label="true"
        :stacked="stacked"
        :options="orderOptions"
        @input="orderUpdated"
      />
    </template>
  </FilterBar>
</template>

<script>
import FilterBar from 'tui/components/filters/FilterBar';
import SelectFilter from 'tui/components/filters/SelectFilter';
import SearchFilter from 'tui/components/filters/SearchFilter';
import { debounce } from 'tui/util';

export default {
  components: {
    FilterBar,
    SelectFilter,
    SearchFilter,
  },

  props: {
    isForArchived: {
      required: true,
      type: Boolean,
    },

    defaultFilterValues: {
      type: Object,
      default() {
        return {
          search: '',
          proficient: null,
        };
      },
    },

    defaultOrder: {
      required: false,
      type: String,
      default: 'alphabetical',
    },
  },

  data() {
    return {
      filters: {
        search: '',
        proficient: null,
      },
      order: 'alphabetical',
    };
  },

  computed: {
    proficientOptions() {
      return [
        { id: null, label: this.$str('all', 'totara_competency') },
        { id: true, label: this.$str('proficient', 'totara_competency') },
        { id: false, label: this.$str('not_proficient', 'totara_competency') },
      ];
    },

    orderOptions() {
      return [
        {
          id: 'alphabetical',
          label: this.$str('sort_alphabetical', 'totara_competency'),
        },
        !this.isForArchived && {
          id: 'recently-assigned',
          label: this.$str('sort_recently_assigned', 'totara_competency'),
        },
        this.isForArchived && {
          id: 'recently-archived',
          label: this.$str('sort_recently_archived', 'totara_competency'),
        },
      ].filter(Boolean);
    },
  },

  watch: {
    defaultFilters(newFilters) {
      this.filters.search = newFilters.search;
      this.filters.proficient = newFilters.proficient;
    },
    defaultOrder(newOrder) {
      this.order = newOrder;
    },
  },

  created() {
    this.filtersUpdatedDebounced = debounce(this.filtersUpdated, 500);
  },

  mounted() {
    this.filters.search = this.defaultFilterValues.search;
    this.filters.proficient = this.defaultFilterValues.proficient;
    this.order = this.defaultOrder;
  },

  methods: {
    filtersUpdated() {
      this.$emit('filters-updated', this.filters);
    },

    orderUpdated() {
      this.$emit('order-updated', this.order);
    },
  },
};
</script>

<lang-strings>
{
  "totara_competency": [
    "proficient",
    "not_proficient",
    "all",
    "sort_alphabetical",
    "sort_recently_archived",
    "sort_recently_assigned",
    "proficiency_status",
    "filter_competencies"
  ],
  "moodle": ["search", "sortby"],
  "admin": ["filters"]
}
</lang-strings>
