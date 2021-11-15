<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @module mod_perform
-->
<template>
  <div class="tui-performUserActivitiesFilter">
    <FilterBar
      v-model="selectedFilters"
      :title="$str('user_activities_filter', 'mod_perform')"
      :has-top-bar="false"
    >
      <template v-slot:filters-left="{ stacked }">
        <SelectFilter
          v-if="filterOptions.activityTypes"
          v-model="selectedFilters.activityType"
          :label="$str('user_activities_filter_type', 'mod_perform')"
          :show-label="true"
          :options="activityTypeFilterOptions"
          :stacked="stacked"
        />
        <SelectFilter
          v-if="filterOptions.progressOptions"
          v-model="selectedFilters.ownProgress"
          :label="$str('user_activities_filter_own_progress', 'mod_perform')"
          :show-label="true"
          :options="progressFilterOptions"
          :stacked="stacked"
        />
        <ToggleSwitch
          v-model="selectedFilters.overdueOnly"
          :text="$str('user_activities_filter_overdue_only', 'mod_perform')"
          :toggle-first="true"
        />
      </template>
    </FilterBar>

    <div class="tui-performUserActivitiesFilter__after">
      <div v-if="total" class="tui-performUserActivitiesFilter__after-total">
        {{ $str('showing_activities', 'mod_perform', { shown, total }) }}
      </div>
    </div>
  </div>
</template>
<script>
import FilterBar from 'tui/components/filters/FilterBar';
import SelectFilter from 'tui/components/filters/SelectFilter';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';

export default {
  components: {
    FilterBar,
    SelectFilter,
    ToggleSwitch,
  },

  props: {
    filterOptions: Object,
    shown: {
      type: Number,
      required: true,
    },
    total: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      selectedFilters: {
        activityType: null,
        ownProgress: null,
        overdueOnly: false,
      },
    };
  },

  computed: {
    activityTypeFilterOptions() {
      return this.mapFilterOptions(this.filterOptions.activityTypes);
    },

    progressFilterOptions() {
      return this.mapFilterOptions(this.filterOptions.progressOptions);
    },
  },

  watch: {
    selectedFilters: {
      /**
       * Emit event on filter value change
       *
       */
      deep: true,
      handler() {
        this.$emit('update-filters', this.selectedFilters);
      },
    },
  },

  methods: {
    /**
     * Map filter options to required format
     *
     * @param {Object} source
     * @return {Object}
     */
    mapFilterOptions(source) {
      let filters = source;

      filters = Object.keys(filters).map(id => {
        return {
          id: id,
          label: filters[id],
        };
      });

      filters.unshift({
        id: null,
        label: this.$str('all', 'core'),
      });

      return filters;
    },
  },
};
</script>
<lang-strings>
  {
    "core": [
      "all"
    ],
    "mod_perform": [
      "showing_activities",
      "user_activities_filter",
      "user_activities_filter_own_progress",
      "user_activities_filter_overdue_only",
      "user_activities_filter_type"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performUserActivitiesFilter {
  & > * + * {
    margin-top: var(--gap-8);
  }

  &__after {
    &-total {
      @include tui-font-heading-x-small;
    }
  }
}

@media (min-width: $tui-screen-sm) {
  .tui-performUserActivitiesFilter {
    &__after {
      display: flex;
    }
  }
}
</style>
