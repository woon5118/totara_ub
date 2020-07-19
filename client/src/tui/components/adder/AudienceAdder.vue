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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <Adder
    :open="open"
    :title="$str('select_audiences', 'totara_core')"
    :existing-items="existingItems"
    :loading="$apollo.loading"
    :show-load-more="nextPage"
    @added="closeWithData($event)"
    @cancel="$emit('cancel')"
    @load-more="loadMoreItems()"
    @selected-tab-active="updateSelectedItems($event)"
  >
    <template v-slot:browse-filters>
      <FilterBar
        :has-top-bar="false"
        :title="$str('filter_audiences', 'totara_core')"
      >
        <template v-slot:filters-left="{ stacked }">
          <SearchFilter
            v-model="filters.search"
            :label="$str('filter_audiences_search_label', 'totara_core')"
            :show-label="false"
            :placeholder="$str('search', 'totara_core')"
            :stacked="stacked"
          />
        </template>
      </FilterBar>
    </template>
    <template v-slot:browse-list="{ disabledItems, selectedItems, update }">
      <SelectTable
        :large-check-box="true"
        :no-label-offset="true"
        :value="selectedItems"
        :data="audiences && audiences.items ? audiences.items : []"
        :disabled-ids="disabledItems"
        checkbox-v-align="center"
        :select-all-enabled="true"
        :border-bottom-hidden="true"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="12" valign="center">
            {{ $str('cohortname', 'totara_cohort') }}
          </HeaderCell>
          <HeaderCell size="4" valign="center">
            {{ $str('shortname', 'totara_cohort') }}
          </HeaderCell>
        </template>

        <template v-slot:row="{ row }">
          <Cell
            size="12"
            :column-header="$str('cohortname', 'totara_cohort')"
            valign="center"
          >
            {{ row.name }}
          </Cell>

          <Cell
            size="4"
            :column-header="$str('shortname', 'totara_cohort')"
            valign="center"
          >
            {{ row.idnumber }}
          </Cell>
        </template>
      </SelectTable>
    </template>

    <template v-slot:basket-list="{ disabledItems, selectedItems, update }">
      <SelectTable
        :large-check-box="true"
        :no-label-offset="true"
        :value="selectedItems"
        :data="audienceSelectedItems"
        :disabled-ids="disabledItems"
        checkbox-v-align="center"
        :border-bottom-hidden="true"
        :select-all-enabled="true"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="12" valign="center">
            {{ $str('cohortname', 'totara_cohort') }}
          </HeaderCell>
          <HeaderCell size="4" valign="center">
            {{ $str('shortname', 'totara_cohort') }}
          </HeaderCell>
        </template>

        <template v-slot:row="{ row }">
          <Cell
            size="12"
            :column-header="$str('cohortname', 'totara_cohort')"
            valign="center"
          >
            {{ row.name }}
          </Cell>

          <Cell
            size="4"
            :column-header="$str('shortname', 'totara_cohort')"
            valign="center"
          >
            {{ row.idnumber }}
          </Cell>
        </template>
      </SelectTable>
    </template>
  </Adder>
</template>

<script>
// Components
import Adder from 'tui/components/adder/Adder';
import Cell from 'tui/components/datatable/Cell';
import FilterBar from 'tui/components/filters/FilterBar';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import SearchFilter from 'tui/components/filters/SearchFilter';
import SelectTable from 'tui/components/datatable/SelectTable';
// Queries
import cohorts from 'core/graphql/cohorts';

export default {
  components: {
    Adder,
    Cell,
    FilterBar,
    HeaderCell,
    SearchFilter,
    SelectTable,
  },

  props: {
    existingItems: {
      type: Array,
      default: () => [],
    },
    open: Boolean,
    customQuery: Object,
  },

  data() {
    return {
      audiences: null,
      audienceSelectedItems: [],
      filters: {
        search: '',
      },
      nextPage: false,
      skipQueries: true,
    };
  },

  watch: {
    /**
     * On opening of adder, unblock query
     *
     */
    open() {
      if (this.open) {
        this.filters.search = '';
        this.skipQueries = false;
      } else {
        this.skipQueries = true;
      }
    },
  },

  /**
   * Apollo queries have been registered here to provide support for custom queries
   */
  created() {
    /**
     * All audiences query
     *
     */
    this.$apollo.addSmartQuery('audiences', {
      query: this.customQuery ? this.customQuery : cohorts,
      skip() {
        return this.skipQueries;
      },
      variables() {
        return {
          query: {
            filters: {
              name: this.filters.search,
            },
          },
        };
      },
      update({ core_cohorts: audiences }) {
        this.nextPage = audiences.next_cursor ? audiences.next_cursor : false;
        return audiences;
      },
    });

    /**
     * Selected audiences query
     *
     */
    this.$apollo.addSmartQuery('selectedAudiences', {
      query: this.customQuery ? this.customQuery : cohorts,
      skip() {
        return this.skipQueries;
      },
      variables() {
        return {
          query: {
            filters: {
              ids: [],
            },
          },
        };
      },
      update({ core_cohorts: selectedAudiences }) {
        this.audienceSelectedItems = selectedAudiences.items;
        return selectedAudiences;
      },
    });
  },

  methods: {
    /**
     * Load addition items and append to list
     *
     */
    async loadMoreItems() {
      if (!this.nextPage) {
        return;
      }

      this.$apollo.queries.audiences.fetchMore({
        variables: {
          query: {
            cursor: this.nextPage,
            filters: {
              name: this.filters.search,
            },
          },
        },

        updateQuery: (previousResult, { fetchMoreResult }) => {
          const oldData = previousResult.core_cohorts;
          const newData = fetchMoreResult.core_cohorts;
          const newList = oldData.items.concat(newData.items);

          return {
            core_cohorts: {
              items: newList,
              next_cursor: newData.next_cursor,
            },
          };
        },
      });
    },

    /**
     * Close the adder, returning the selected items data
     *
     * @param {Array} selection
     */
    async closeWithData(selection) {
      let data;
      try {
        data = await this.updateSelectedItems(selection);
      } catch (error) {
        console.error(error);
        return;
      }
      this.$emit('added', { ids: selection, data: data });
    },

    /**
     * Update the selected items data
     *
     * @param {Array} selection
     */
    async updateSelectedItems(selection) {
      const numberOfItems = selection.length;

      try {
        await this.$apollo.queries.selectedAudiences.refetch({
          query: {
            filters: {
              ids: selection,
            },
            result_size: numberOfItems,
          },
        });
      } catch (error) {
        console.error(error);
      }
      return this.audienceSelectedItems;
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "filter_audiences",
    "filter_audiences_search_label",
    "select_audiences",
    "search"
  ],
  "totara_cohort": [
    "cohortname",
    "shortname"
  ]
}
</lang-strings>
