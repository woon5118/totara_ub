<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <FilterBar
    v-model="selectedFilters"
    :styleclass="{
      lastItemRight: true,
    }"
  >
    <SelectFilter
      v-if="filterOptions.competency_type"
      v-model="selectedFilters.competencyType"
      :label="$str('filter:competency_type', 'totara_competency')"
      :show-label="true"
      :options="competencyTypeFilterOptions"
      @input="filtersSelected = true"
    />
    <SelectFilter
      v-if="filterOptions.assignment_reason"
      v-model="selectedFilters.assignmentReason"
      :label="$str('filter:reason_assigned', 'pathway_manual')"
      :show-label="true"
      :options="assignmentReasonFilterOptions"
      @input="filtersSelected = true"
    />
    <SelectFilter
      v-if="filterOptions.rating_history"
      v-model="selectedFilters.ratingHistory"
      :label="$str('filter:rating_history', 'pathway_manual')"
      :show-label="true"
      :options="ratingHistoryFilterOptions"
      @input="filtersSelected = true"
    />
    <div class="tui-selectFilter" style="flex-shrink: 0">
      <Button
        :text="$str('filter:update_selection', 'pathway_manual')"
        :styleclass="{ small: true }"
        :disabled="!filtersSelected"
        @click="updateFiltersWithWarning"
      />
    </div>
    <ConfirmModal
      :open="showConfirmFiltersModal"
      :title="$str('modal:confirm_update_filters_title', 'pathway_manual')"
      @confirm="updateFilters"
      @cancel="showConfirmFiltersModal = false"
    >
      <span
        v-html="$str('modal:confirm_update_filters_body', 'pathway_manual')"
      />
    </ConfirmModal>
  </FilterBar>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ConfirmModal from 'pathway_manual/components/ConfirmModal';
import FilterBar from 'totara_core/components/filters/FilterBar';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: { Button, ConfirmModal, FilterBar, SelectFilter },

  props: {
    filterOptions: {
      required: true,
      type: Object,
    },
    hasRatings: {
      required: true,
      type: Boolean,
    },
  },

  data() {
    return {
      selectedFilters: {
        competencyType: 0,
        assignmentReason: 0,
        ratingHistory: 0,
      },
      assignmentKeys: {},
      showConfirmFiltersModal: false,
      filtersSelected: false,
    };
  },

  computed: {
    assignmentReasonFilterOptions() {
      let filters = this.filterOptions.assignment_reason;

      filters = filters.map(reason => {
        return {
          id: reason.key,
          label: reason.reason,
        };
      });
      filters.unshift({
        id: 0,
        label: this.$str('all'),
      });

      return filters;
    },

    competencyTypeFilterOptions() {
      let filters = this.filterOptions.competency_type;

      filters = filters.map(type => {
        return {
          id: type.id,
          label: type.display_name,
        };
      });
      filters.unshift({
        id: 0,
        label: this.$str('all'),
      });

      return filters;
    },

    ratingHistoryFilterOptions() {
      return [
        {
          id: 0,
          label: this.$str('all'),
        },
        {
          id: -1,
          label: this.$str('filter:never_rated', 'pathway_manual'),
        },
        {
          id: 1,
          label: this.$str('filter:previously_rated', 'pathway_manual'),
        },
      ];
    },
  },

  mounted() {
    this.assignmentKeys = {};
    let assignmentReason = this.filterOptions.assignment_reason;
    for (let i = 0; i < assignmentReason.length; i++) {
      let reason = assignmentReason[i];
      this.assignmentKeys[reason.key] = reason.assignments.map(
        assignment => assignment.id
      );
    }
  },

  methods: {
    updateFiltersWithWarning() {
      if (this.hasRatings) {
        this.showConfirmFiltersModal = true;
        return;
      }

      this.updateFilters();
    },

    updateFilters() {
      let filters = {};

      if (this.selectedFilters.competencyType !== 0) {
        filters.competency_type = this.selectedFilters.competencyType;
      }
      if (this.selectedFilters.assignment !== 0) {
        filters.assignment_reason = this.assignmentKeys[
          this.selectedFilters.assignmentReason
        ];
      }
      if (this.selectedFilters.ratingHistory !== 0) {
        filters.rating_history = this.selectedFilters.ratingHistory > 0;
      }

      this.filtersSelected = false;
      this.showConfirmFiltersModal = false;

      this.$emit('update-filters', filters);
    },
  },

  apollo: {},
};
</script>

<lang-strings>
  {
    "moodle": [
      "all"
    ],
    "pathway_manual": [
      "filter:never_rated",
      "filter:previously_rated",
      "filter:rating_history",
      "filter:reason_assigned",
      "filter:update_selection",
      "modal:confirm_update_filters_body",
      "modal:confirm_update_filters_title"
    ],
    "totara_competency": [
      "filter:competency_type"
    ]
  }
</lang-strings>
