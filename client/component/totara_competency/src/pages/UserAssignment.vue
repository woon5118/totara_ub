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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_competency
-->

<template>
  <div>
    <a :href="goBackLink">{{ goBackText }}</a>

    <h2>{{ pageHeading }}</h2>

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
      :loading="isSaving"
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
import ClearIcon from 'tui/components/icons/Clear';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import CreateUserAssignmentMutation from 'totara_competency/graphql/create_user_assignments';
import FilterSidePanel from 'tui/components/filters/FilterSidePanel';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loading/Loader';
import MultiSelectFilter from 'tui/components/filters/MultiSelectFilter';
import SearchFilter from 'tui/components/filters/SearchFilter';
import SelectFilter from 'tui/components/filters/SelectFilter';
import SelectionTable from 'totara_competency/components/user_assignment/SelectionTable';
import UserAssignableCompetenciesQuery from 'totara_competency/graphql/user_assignable_competencies';
import { notify } from 'tui/notifications';

export default {
  components: {
    Basket,
    Button,
    ButtonIcon,
    ClearIcon,
    ConfirmationModal,
    FilterSidePanel,
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
      try {
        const { data: result } = await this.assign();
        this.isSaving = false;

        const assignedCount =
          result.totara_competency_create_user_assignments.length;
        if (assignedCount === 0) {
          this.showErrorNotification();
        } else {
          window.location.href = this.$url(this.goBackLink, {
            assign_success: assignedCount,
          });
        }
      } catch (e) {
        this.showErrorNotification();
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

    /**
     * Show generic save/update error toast.
     */
    showErrorNotification() {
      notify({
        message: this.$str('error_generic_mutation', 'totara_competency'),
        type: 'error',
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

<style lang="scss">
.tui-competencySelfAssignment {
  &__table {
    & > * + * {
      margin-top: var(--gap-2);
    }
  }

  &__tableExpand {
    & > * + * {
      margin: var(--gap-2) 0 0;
    }

    &-header {
      @include tui-font-heading-small();
    }

    &-subHeader {
      @include tui-font-heading-x-small();
    }
  }

  &__actions {
    justify-content: space-between;
    margin-bottom: var(--gap-5);
  }

  &__header {
    margin: 0;
    @include tui-font-heading-small();
  }

  &__table {
    margin-bottom: var(--gap-5);
  }
}
</style>
