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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_competency
-->

<template>
  <div>
    <a :href="goBackLink">{{ goBackText }}</a>

    <h2>{{ pageHeading }}</h2>

    <!-- TODO use alert component when created -->
    <div
      v-if="mutationError"
      class="alert alert-danger alert-with-icon alert-dismissable fade-in"
      role="alert"
    >
      <button type="button" class="close" data-dismiss="alert">
        <FlexIcon icon="delete-ns" />
      </button>
      <div class="alert-icon">
        <FlexIcon icon="notification-error" />
      </div>
      <div class="alert-message">
        {{ $str('error_generic_mutation', 'totara_competency') }}
      </div>
    </div>

    <Grid :stack-at="900" class="tui-competencySelfAssignment__actions">
      <GridItem :units="3">
        <SelectFilter
          v-if="frameworks.length > 1 && !isViewingSelections"
          v-model="frameworkSelection"
          :label="$str('competencyframeworks', 'totara_hierarchy')"
          :show-label="false"
          :options="frameworkOptions"
        />
      </GridItem>
      <GridItem :units="6">
        <Basket :items="selectedItems" :bulk-actions="basketActions">
          <template v-slot:status="{ empty }">
            <ButtonIcon
              v-if="isViewingSelections && !empty"
              :styleclass="{ small: true }"
              :text="$str('clearall', 'totara_core')"
              :aria-label="$str('clearall', 'totara_core')"
              @click="clearSelections"
            >
              <ClearIcon />
            </ButtonIcon>
          </template>
          <template v-slot:actions="{ empty }">
            <Button
              v-if="!isViewingSelections && !empty"
              :styleclass="{ transparent: true }"
              :text="$str('viewselected', 'totara_core')"
              @click="viewSelections"
            />
            <Button
              v-if="isViewingSelections && !empty"
              :styleclass="{ transparent: true }"
              :text="$str('back_to_all_competencies', 'totara_competency')"
              @click="applyFilter"
            />
          </template>
        </Basket>
      </GridItem>
    </Grid>

    <Grid
      :stack-at="900"
      class="tui-competencySelfAssignment__filter-table-grid"
    >
      <GridItem v-if="!isViewingSelections" :units="3">
        <FilterSidePanel
          v-model="filters"
          :title="$str('filter_competencies', 'totara_competency')"
          @active-count-change="activeFilterCount = $event"
        >
          <SearchFilter
            v-model="filters.text"
            :label="$str('search', 'totara_core')"
            :placeholder="$str('search', 'totara_core')"
            :show-label="false"
            :stacked="true"
          />

          <MultiSelectFilter
            v-if="types.length > 1"
            v-model="filters.type"
            :title="$str('competencytypes', 'totara_hierarchy')"
            :show-label="true"
            :options="competencyTypeOptions"
          />

          <SelectFilter
            v-model="filters.assignment_status"
            :label="$str('header_assignment_status', 'totara_competency')"
            :show-label="true"
            :stacked="true"
            :options="assignmentStatusesOptions"
          />
        </FilterSidePanel>
      </GridItem>
      <GridItem :units="9" grows>
        <Loader :loading="$apollo.loading">
          <SelectionTable
            v-if="hasLoadedCompetencies"
            v-model="selectedItems"
            :competencies="data.items"
            :total-competency-count="data.total"
            class="tui-competencySelfAssignment__table"
          />
          <Button
            v-if="shouldShowLoadMoreButton"
            :text="$str('loadmore', 'totara_core')"
            @click="loadMore"
          />
        </Loader>
      </GridItem>
    </Grid>

    <ConfirmationModal
      :title="$str('assign_competencies', 'totara_competency')"
      :open="isShowingConfirmationModal"
      @confirm="confirmAssignment"
      @cancel="isShowingConfirmationModal = false"
    >
      <p>
        {{ selectionDescription }}
      </p>

      <p>
        {{ $str('confirm_generic', 'totara_competency') }}
      </p>
    </ConfirmationModal>
  </div>
</template>

<script>
import Basket from 'tui/components/basket/Basket';
import Button from 'tui/components/buttons/Button';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import ClearIcon from 'tui/components/icons/common/Clear';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import CreateUserAssignmentMutation from 'totara_competency/graphql/create_user_assignments';
import FilterSidePanel from 'tui/components/filters/FilterSidePanel';
import FlexIcon from 'tui/components/icons/FlexIcon';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loader/Loader';
import MultiSelectFilter from 'tui/components/filters/MultiSelectFilter';
import SearchFilter from 'tui/components/filters/SearchFilter';
import SelectFilter from 'tui/components/filters/SelectFilter';
import SelectionTable from 'totara_competency/components/user_assignment/SelectionTable';
import UserAssignableCompetenciesQuery from 'totara_competency/graphql/user_assignable_competencies';

export default {
  components: {
    Basket,
    Button,
    ButtonIcon,
    ClearIcon,
    ConfirmationModal,
    FilterSidePanel,
    FlexIcon,
    Grid,
    GridItem,
    Loader,
    MultiSelectFilter,
    SearchFilter,
    SelectFilter,
    SelectionTable,
  },
  props: {
    basePageHeading: {
      required: true,
      type: String,
    },
    goBackLink: {
      required: true,
      type: String,
    },
    goBackText: {
      required: true,
      type: String,
    },
    frameworks: {
      required: true,
      type: Array,
    },
    types: {
      required: true,
      type: Array,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data() {
    // The filter side panel count currently only supports empty string and empty array as a "not set" value,
    // so it's an important that id is empty string rather than null or undefined.
    const STATUS_ANY = '';
    const STATUS_ASSIGNED = 1;
    const STATUS_NOT_ASSIGNED = 0;

    const statusAnyLabel = this.$str('any', 'totara_competency');
    const statusAssignedLabel = this.$str(
      'currently_assigned',
      'totara_competency'
    );
    const statusNotAssignedLabel = this.$str(
      'not_assigned',
      'totara_competency'
    );

    return {
      allSelected: false,
      selectedItems: [],
      isViewingSelections: false,
      isShowingConfirmationModal: false,
      isSaving: false,
      mutationError: null,
      data: {
        items: [],
        total: null,
        next_cursor: null,
      },
      frameworkSelection: null,
      filters: {
        assignment_status: STATUS_ANY,
        text: '',
        type: [],
      },
      activeFilterCount: 0,
      assignmentStatusesOptions: [
        {
          id: STATUS_ANY,
          label: statusAnyLabel,
        },
        {
          id: STATUS_ASSIGNED,
          label: statusAssignedLabel,
        },
        {
          id: STATUS_NOT_ASSIGNED,
          label: statusNotAssignedLabel,
        },
      ],
      basketActions: [
        {
          label: this.$str('assign_competencies', 'totara_competency'),
          action: this.showConfirmationModal,
        },
      ],
    };
  },
  computed: {
    pageHeading() {
      if (this.isViewingSelections) {
        return this.$str('view_selected_competencies', 'totara_competency');
      }

      return this.basePageHeading;
    },
    hasLoadedCompetencies() {
      return this.data && this.data.total !== null;
    },
    firstPageQueryVariables() {
      return {
        user_id: this.userId,
        filters: this.filtersForQuery,
        cursor: null,
      };
    },
    subsequentPageQueryVariables() {
      return {
        user_id: this.userId,
        filters: this.filtersForQuery,
        cursor: this.data.next_cursor,
      };
    },
    filtersForQuery() {
      // The graphql filter parameter expects, the "side panel filters"
      // and selected framework to be combined into one object
      return Object.assign({}, this.filters, {
        framework: this.frameworkSelection,
      });
    },
    hasEmptyResult() {
      return this.data && this.data.total === 0;
    },
    isViewingDefaultOptions() {
      // Are we on the "any" framework selection
      if (this.frameworkSelection !== null) {
        return false;
      }

      return this.activeFilterCount === 0;
    },
    competencyTypeOptions() {
      return this.types.map(type => {
        return { id: type.id, label: type.name };
      });
    },
    frameworkOptions() {
      const all = {
        id: null,
        label: this.$str(
          'filter_framework_all_frameworks',
          'totara_competency'
        ),
      };

      const frameworks = this.frameworks.map(framework => {
        return { id: framework.id, label: framework.name };
      });

      return [all, ...frameworks];
    },
    shouldShowLoadMoreButton() {
      return this.data.items.length > 0 && this.data.next_cursor !== '';
    },
    selectionDescription() {
      if (this.selectedItems.length === 1) {
        return this.$str(
          'one_competency_selected',
          'totara_competency',
          this.selectedItems.length
        );
      }

      return this.$str(
        'n_competencies_selected',
        'totara_competency',
        this.selectedItems.length
      );
    },
  },
  watch: {
    frameworkSelection: 'applyFilter',
    filters: {
      deep: true,
      handler: 'applyFilter',
    },
    data() {
      // Remove genetic mutation error message when data is re-fetched.
      this.mutationError = null;
    },
  },
  methods: {
    async viewSelections() {
      await this.$apollo.queries.data.refetch({
        user_id: this.userId,
        filters: {
          ids: this.selectedItems,
        },
      });

      this.isViewingSelections = true;
    },
    async clearSelections() {
      this.resetFilters();
      this.selectedItems = [];
    },
    resetFilters() {
      this.filters = this.$options.data.call(this).filters;
    },
    showConfirmationModal() {
      this.isShowingConfirmationModal = true;
    },
    async confirmAssignment() {
      await this.tryAssign();

      this.isShowingConfirmationModal = false;
    },
    async tryAssign() {
      this.isSaving = true;
      this.mutationError = null;

      try {
        await this.assign();

        // TODO when adding proper notifications in please note:
        // Due to this being a batch api designed to tolerate partial success,
        // single assignment can silently fail, indicated by no results being returned.
        // We may want to warn the user of only partial success.
        window.location.href = this.goBackLink;
      } catch (e) {
        this.mutationError = e;
      } finally {
        this.isSaving = false;
      }
    },
    assign() {
      return this.$apollo.mutate({
        mutation: CreateUserAssignmentMutation,
        variables: {
          user_id: this.userId,
          competency_ids: this.selectedItems,
        },
        // Don't refetch because we are going to redirect as soon as the mutation response comes back
        refetchAll: false,
      });
    },
    async applyFilter() {
      const refetch = await this.$apollo.queries.data.refetch(
        this.firstPageQueryVariables
      );

      this.isViewingSelections = false;

      return refetch;
    },
    loadMore() {
      // Fetch more data and transform the original result
      this.$apollo.queries.data.fetchMore({
        variables: this.subsequentPageQueryVariables,

        updateQuery: (previousResult, { fetchMoreResult }) => {
          previousResult =
            previousResult.totara_competency_user_assignable_competencies;

          const result =
            fetchMoreResult.totara_competency_user_assignable_competencies;

          return {
            totara_competency_user_assignable_competencies: {
              __typename: previousResult.__typename,
              items: [...previousResult.items, ...result.items],
              total: result.total,
              next_cursor: result.next_cursor,
            },
          };
        },
      });
    },
  },
  apollo: {
    data() {
      return {
        query: UserAssignableCompetenciesQuery,
        variables: this.firstPageQueryVariables,
        update({ totara_competency_user_assignable_competencies: data }) {
          return data;
        },
      };
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "loadmore",
      "search",
      "viewselected"
    ],
    "totara_hierarchy": [
      "assign",
      "competencyframeworks",
      "competencytypes"
    ],
    "totara_competency": [
      "all",
      "any",
      "assign_competencies",
      "back_to_all_competencies",
      "competencies",
      "confirm_generic",
      "currently_assigned",
      "error_generic_mutation",
      "filter_framework_all_frameworks",
      "filter_competencies",
      "header_assignment_status",
      "n_competencies_selected",
      "no_competency_to_assign",
      "not_assigned",
      "one_competency_selected",
      "view_selected_competencies"
    ]
  }
</lang-strings>
