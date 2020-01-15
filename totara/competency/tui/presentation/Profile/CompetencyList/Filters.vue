<template>
  <!-- Filters bar -->
  <div class="tui-CompetencyProfileListFilters">
    <div class="tui-CompetencyProfileListFilters__left">
      <FlexIcon icon="preferences" size="500" alt="Filters" />
      <template v-if="!isForArchived">
        <label
          for="competency-profile-proficient-filter"
          style="line-height: 34px;"
        >
          Proficient status
          <!-- TODO lang string -->
        </label>
        <select
          id="competency-profile-proficient-filter"
          v-model="filters.proficient"
          @change="filtersUpdated"
        >
          <option :value="null" v-text="$str('all', 'totara_competency')" />
          <option
            :value="true"
            v-text="$str('proficient', 'totara_competency')"
          />
          <option
            :value="false"
            v-text="$str('not_proficient', 'totara_competency')"
          />
        </select>
      </template>
      <label class="sr-only" for="competency-profile-proficient-text-search">
        Ugly search text input
        <!-- TODO lang string -->
      </label>
      <input
        id="competency-profile-proficient-text-search"
        v-model.lazy="filters.search"
        class="tui-CompetencyProfileListFilters__input"
        type="text"
        :placeholder="$str('search')"
        @change="filtersUpdated"
      />
    </div>
    <div class="tui-CompetencyProfileListFilters__right">
      <label for="competency-profile-order-assignments-by">
        Sort by
        <!-- TODO lang string -->
      </label>
      <select
        id="competency-profile-order-assignments-by"
        v-model="order"
        @change="orderUpdated"
      >
        <option
          value="alphabetical"
          v-text="$str('sort:alphabetical', 'totara_competency')"
        />
        <option
          v-if="!isForArchived"
          value="recently-assigned"
          v-text="$str('sort:recently_assigned', 'totara_competency')"
        />
        <option
          v-if="isForArchived"
          value="recently-archived"
          v-text="$str('sort:recently_archived', 'totara_competency')"
        />
      </select>
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';

export default {
  components: {
    FlexIcon,
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

  watch: {
    defaultFilters(newFilters) {
      this.filters.search = newFilters.search;
      this.filters.proficient = newFilters.proficient;
    },
    defaultOrder(newOrder) {
      this.order = newOrder;
    },
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
<style lang="scss">
.tui-CompetencyProfileListFilters {
  display: flex;
  flex-direction: row;
  justify-content: space-between;

  &__left {
    display: flex;
    flex-direction: row;
    justify-content: left;
    line-height: 34px;

    > *:not(:last-child) {
      margin-right: 1rem;
    }
  }

  &__right {
    display: flex;
    flex-direction: row;
    justify-content: right;
    line-height: 34px;
    > *:not(:last-child) {
      margin-right: 1rem;
    }
  }

  //TODO: temporary styling for an input component
  &__input {
    height: 34px;
    padding: 6px 12px;
    border: 1px #ccc solid;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  }
}
</style>
<lang-strings>
    {
      "totara_competency": ["proficient", "not_proficient", "all", "sort:alphabetical", "sort:recently_archived", "sort:recently_assigned"],
      "moodle": ["search"]
    }
</lang-strings>
