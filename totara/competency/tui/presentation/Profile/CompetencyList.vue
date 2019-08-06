<template>
  <div>
    <hr />
    <!-- Filters bar -->
    <div
      style="display: flex; flex-direction: row; justify-content: space-between;"
    >
      <div
        style="justify-content: left; display: flex; flex-direction: row;"
        class="totara_competency-profile__comp-list-left-filters"
      >
        <FlexIcon icon="preferences" size="500" alt="Filters" />
        <template v-if="filterNotArchived">
          <label
            for="competency-profile-proficient-filter"
            style="line-height: 34px;"
          >
            Proficient status
            <!-- TODO lang string -->
          </label>
          <select
            id="competency-profile-proficient-filter"
            v-model="proficientFilter"
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
          v-model.lazy="searchFilter"
          class="totara_competency-profile__input"
          type="text"
          :placeholder="$str('search')"
        />
      </div>
      <div
        style="justify-content: right; display: flex; flex-direction: row; line-height: 34px;"
        class="totara_competency-profile__comp-list-left-filters"
      >
        <label for="competency-profile-order-assignments-by">
          Sort by
          <!-- TODO lang string -->
        </label>
        <select id="competency-profile-order-assignments-by" v-model="order">
          <option
            value="alphabetical"
            v-text="$str('sort:alphabetical', 'totara_competency')"
          />
          <option
            v-if="filterNotArchived"
            value="recently-assigned"
            v-text="$str('sort:recently_assigned', 'totara_competency')"
          />
          <option
            v-if="!filterNotArchived"
            value="recently-archived"
            v-text="$str('sort:recently_archived', 'totara_competency')"
          />
        </select>
      </div>
    </div>
    <hr />
    <List
      v-if="filterNotArchived"
      :columns="competencyColumns"
      :data="competencies"
    >
      <template v-slot:column-name="props">
        <div>
          <a
            :href="competencyDetailsLink(props.row)"
            v-text="props.row.competency.fullname"
          />
        </div>
      </template>
      <template v-slot:column-proficient="props">
        <template v-if="props.row.items[0].proficient">
          <FlexIcon icon="check" alt="//TODO add something here" />
        </template>
      </template>
      <template v-slot:column-rating="props">
        <MyRatingCell
          v-if="props.row.items[0].my_value"
          :value="props.row.items[0].my_value"
          :scales="scales"
        />
      </template>
    </List>
    <List
      v-else
      :columns="archivedCompetencyColumns"
      :data="competencies"
      bg-color="gray"
    >
      <template v-slot:column-name="props">
        <div>
          <a
            :href="competencyDetailsLink(props.row)"
            v-text="props.row.competency.fullname"
          />
        </div>
        <ul
          class="tui-CompetencyList__archived-assignments-list tui-CompetencyList__archived-assignments-list-padded"
        >
          <li v-for="(item, key) in props.row.items" :key="key">
            <span v-text="item.assignment.progress_name" />
          </li>
        </ul>
      </template>
      <template v-slot:column-archived-date="props">
        <ul class="tui-CompetencyList__archived-assignments-list">
          <li v-for="(item, key) in props.row.items" :key="key">
            <span v-text="item.assignment.archived_at" />
          </li>
        </ul>
      </template>
      <template v-slot:column-proficient="props">
        <ul class="tui-CompetencyList__archived-assignments-list">
          <li v-for="(item, key) in props.row.items" :key="key">
            <template v-if="item.proficient">
              <FlexIcon icon="check" alt="//TODO add something here" />
            </template>
          </li>
        </ul>
      </template>
      <template v-slot:column-rating="props">
        <ul class="tui-CompetencyList__archived-assignments-list">
          <li v-for="(item, key) in props.row.items" :key="key">
            <MyRatingCell
              v-if="item.my_value"
              :value="item.my_value"
              :scales="scales"
            />
          </li>
        </ul>
      </template>
    </List>
  </div>
</template>

<script>
import List from '../../container/List';
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import MyRatingCell from './MyRatingCell';

import CompetencyProgressQuery from '../../../webapi/ajax/competency_progress_for_user.graphql';
import CompetencyScalesQuery from '../../../webapi/ajax/scales.graphql';

let competencyColumns = [
  {
    key: 'name',
    value: 'competency.fullname',
    title: 'Competency',
    grow: true,
    size: 'md',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'xs',
    alignment: ['center'],
  },
  {
    key: 'rating',
    title: 'Rating',
    size: 'sm',
  },
];

let archivedCompetencyColumns = [
  {
    key: 'name',
    value: 'competency.fullname',
    title: 'Competency',
    grow: true,
    size: 'md',
  },
  {
    key: 'archived-date',
    title: 'Archived date',
    size: 'sm',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'xs',
    alignment: 'center',
  },
  {
    key: 'rating',
    title: 'Rating',
    size: 'sm',
  },
];

export default {
  components: {
    MyRatingCell,
    FlexIcon,
    List,
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
      isLoading: false,
      order: 'alphabetical',
      proficientFilter: null,
      searchFilter: '',
      scales: [],
    };
  },

  computed: {
    competencyColumns() {
      return competencyColumns;
    },

    archivedCompetencyColumns() {
      return archivedCompetencyColumns;
    },

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
    competencyDetailsLink(row) {
      let link = `${this.baseUrl}/details/?competency_id=${row.competency.id}`;

      if (!this.isMine) {
        link += `&user_id=${this.userId}`;
      }

      return link;
    },

    displayAssignmentsList(row) {
      let names = [];

      row.items.forEach(({ assignment }) => {
        names.push(this.shortenText(assignment.progress_name, 50));
      });

      return names.join(', ');
    },

    shortenText(str, maxLen, separator = ' ') {
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

.totara_competency-profile__input {
  border: 1px #ccc solid;
  border-radius: 4px;
  padding: 6px 12px;
  transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  height: 34px;
}

.totara_competency-profile__comp-list-left-filters {
  > *:not(:last-child) {
    margin-right: 1rem;
  }
}

.tui-CompetencyList__ {
  &archived-assignments-list {
    display: flex;
    padding: 0;
    margin: 0;
    list-style: none;
    flex-grow: 1;
    flex-wrap: wrap;

    &-padded {
      margin-left: 2rem;
    }
  }
}
</style>
<lang-strings>
    {
      "totara_competency": ["proficient", "not_proficient", "all", "sort:alphabetical", "sort:recently_archived", "sort:recently_assigned"],
      "moodle": ["search"]
    }
</lang-strings>
