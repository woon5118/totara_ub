<template>
  <div>
    <Preloader :display="$apollo.loading" />
    <a :href="goBackLink">{{
      $str('back_to_competency_profile', 'totara_competency')
    }}</a>
    <h2>{{ $str('assign_competencies', 'totara_competency') }}</h2>
    <div v-if="hasEmptyResult && !isFiltered">
      <div class="alert alert-info alert-with-icon">
        <!-- TODO bootstrap alert -->
        <div class="alert-icon">
          <FlexIcon icon="notification-info" />
        </div>
        <div
          class="alert-message"
          v-text="$str('no_competency_to_assign', 'totara_competency')"
        />
      </div>
    </div>
    <div v-else-if="data.total !== null">
      <div>
        <label
          for="competency-profile-assignment-text-filter"
          style="line-height: 34px;"
        >
          {{ $str('search', 'totara_core') }}
        </label>
        <input
          id="competency-profile-assignment-text-filter"
          v-model="filters.text"
          type="text"
          @change="filterUpdated"
        />
      </div>
      <label
        for="competency-profile-frameworks-filter"
        style="line-height: 34px;"
      >
        {{ $str('competencyframeworks', 'totara_hierarchy') }}
      </label>
      <select
        id="competency-profile-frameworks-filter"
        v-model="filters.framework"
        @change="filterUpdated"
      >
        <option :value="null">{{ $str('all', 'totara_competency') }}</option>
        <option
          v-for="framework in frameworks"
          :key="framework.id"
          :value="framework.id"
          v-text="framework.name"
        />
      </select>
      <label for="competency-profile-type-filter" style="line-height: 34px;">
        {{ $str('competencytypes', 'totara_hierarchy') }}
      </label>
      <select
        id="competency-profile-type-filter"
        v-model="filters.assignment_type"
        multiple
        size="3"
        @change="filterUpdated"
      >
        <option :value="null">{{ $str('all', 'totara_competency') }}</option>
        <option
          v-for="type in types"
          :key="type.id"
          :value="type.id"
          v-text="type.name"
        />
      </select>
      <label for="competency-profile-status-filter" style="line-height: 34px;">
        {{ $str('header:assignment_status', 'totara_competency') }}
      </label>
      <select
        id="competency-profile-status-filter"
        v-model="filters.assignment_status"
        multiple
        size="3"
        @change="filterUpdated"
      >
        <option :value="null">{{ $str('all', 'totara_competency') }}</option>
        <option :value="1" v-text="$str('assigned', 'totara_competency')" />
        <option :value="0" v-text="$str('unassigned', 'totara_competency')" />
      </select>
      <div style=" width: 100%;text-align: right;">
        <button
          class="tw-selectionBasket__btn tw-selectionBasket__btn_small tw-selectionBasket__btn_prim"
          :disabled="selectedItems.length == 0"
          @click="assign"
        >
          {{ $str('assign', 'totara_hierarchy') }}
        </button>
      </div>
      <strong>{{
        $str('competencies', 'totara_competency', data.total)
      }}</strong>
      <table class="table table-hover table-striped">
        <thead>
          <th style="width: 5%">
            <input
              v-model="allSelected"
              type="checkbox"
              name="selectall"
              @click="selectAll"
            />
          </th>
          <th style="width: 50%">
            {{ $str('header:competency_name', 'totara_competency') }}
          </th>
          <th style="width: 15%">
            {{ $str('header:assignment_status', 'totara_competency') }}
          </th>
          <th style="width: 30%">
            {{ $str('header:assignment_reasons', 'totara_competency') }}
          </th>
        </thead>
        <tbody>
          <tr v-for="item in data.items" :key="item.id">
            <td>
              <input
                v-model="selectedItems"
                type="checkbox"
                :value="item.id"
                name="assignselect"
              />
            </td>
            <td>{{ item.display_name }}</td>
            <td v-if="isAssigned(item)">
              {{ $str('assigned', 'totara_competency') }}
            </td>
            <td v-else>{{ $str('unassigned', 'totara_competency') }}</td>
            <td>
              <ul>
                <li v-for="(type, key) in getReasonAssigned(item)" :key="key">
                  {{ type }}
                </li>
              </ul>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr v-if="data.next_cursor !== ''">
            <td colspan="5">
              <button @click="loadMore">
                {{ $str('loadmore', 'totara_core') }}
              </button>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
import SelfAssignableCompetenciesQuery from '../../webapi/ajax/self_assignable_competencies.graphql';
import CreateUserAssignmentMutation from '../../webapi/ajax/create_user_assignments.graphql';
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import Preloader from 'totara_competency/presentation/Preloader';

const initial_cursor = window.btoa(
  JSON.stringify({
    limit: 5,
    columns: null,
  })
);

export default {
  components: {
    Preloader,
    FlexIcon,
  },
  props: {
    frameworks: {
      required: true,
      type: Array,
    },
    goBackLink: {
      required: true,
      type: String,
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

  data: function() {
    return {
      allSelected: false,
      selectedItems: [],
      data: {
        items: [],
        total: null,
        next_cursor: null,
      },
      filters: {
        assignment_status: [],
        text: null,
        framework: null,
        assignment_type: [],
      },
      filtered: false,
    };
  },

  computed: {
    encodedCursor() {
      let encodedCursor = this.data.next_cursor;

      // On first load use new cursor
      if (encodedCursor === null) {
        let cursor = {
          limit: 5,
          columns: null,
        };
        encodedCursor = window.btoa(JSON.stringify(cursor));
      }

      return encodedCursor;
    },
    hasEmptyResult() {
      return this.data.total === 0;
    },
    isFiltered() {
      return this.filtered === true;
    },
  },

  methods: {
    assign: function() {
      const confirmMsg =
        'You have selected ' +
        this.selectedItems.length +
        ' competencies to assign.\n\nDo you want to continue?';

      let result = confirm(confirmMsg);
      if (result) {
        this.$apollo
          .mutate({
            // Query
            mutation: CreateUserAssignmentMutation,
            // Parameters
            variables: {
              user_id: this.userId,
              competency_ids: this.selectedItems,
            },
          })
          .then(data => {
            if (
              data.data &&
              data.data.totara_competency_create_user_assignments
            ) {
              var result = data.data.totara_competency_create_user_assignments;
              if (result.length > 0) {
                // TODO Handle notification which should be displayed on the next page here?
                window.location.href = this.goBackLink;
              }
              // TODO Handle case when no result is returned
            }
          })
          .catch(error => {
            // TODO Handle error case
            console.log('error');
            console.error(error);
          });
      }
    },

    filterUpdated: function() {
      this.filtered = true;
      this.$apollo.queries.data.refetch({
        user_id: this.userId,
        cursor: initial_cursor,
        filters: this.filters,
      });
    },

    getReasonAssigned: function(competency) {
      var groupedAssignments = [];
      if (competency.user_assignments) {
        competency.user_assignments.forEach(function(assignment) {
          groupedAssignments.push(assignment.reason_assigned);
        });
      }
      return groupedAssignments;
    },

    isAssigned: function(competency) {
      return (
        competency.user_assignments && competency.user_assignments.length > 0
      );
    },

    isSelfAssigned: function(competency) {
      if (competency.user_assignments) {
        let self = competency.user_assignments.find(function(assignment) {
          return assignment.type === 'self';
        });
        return typeof self !== 'undefined';
      }
      return false;
    },

    loadMore: function() {
      // Fetch more data and transform the original result
      this.$apollo.queries.data.fetchMore({
        // New variables
        variables: {
          user_id: this.userId,
          cursor: this.encodedCursor,
          filters: this.filters,
        },

        updateQuery: (prev, { fetchMoreResult }) => {
          var prevRes = prev.totara_competency_self_assignable_competencies;
          var res =
            fetchMoreResult.totara_competency_self_assignable_competencies;
          const newItems = res.items;
          const total = res.total;
          const nextCursor = res.next_cursor;

          return {
            totara_competency_self_assignable_competencies: {
              __typename: prevRes.__typename,
              items: [...prevRes.items, ...newItems],
              total: total,
              next_cursor: nextCursor,
            },
          };
        },
      });
    },

    selectAll: function() {
      this.selectedItems = [];
      if (!this.allSelected) {
        for (let item in this.data.items) {
          if (Object.prototype.hasOwnProperty.call(this.data.items, item)) {
            this.selectedItems.push(this.data.items[item].id);
          }
        }
      }
    },
  },

  apollo: {
    data: {
      query: SelfAssignableCompetenciesQuery,

      variables() {
        return {
          user_id: this.userId,
          cursor: initial_cursor,
        };
      },

      update({ totara_competency_self_assignable_competencies: data }) {
        return data;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "loadmore",
      "search"
    ],
    "totara_hierarchy": [
      "assign",
      "competencyframeworks",
      "competencytypes"
    ],
    "totara_competency": [
      "all",
      "assigned",
      "assign_competencies",
      "back_to_competency_profile",
      "competencies",
      "header:competency_name",
      "header:assignment_status",
      "header:assignment_reasons",
      "no_competency_to_assign",
      "search_competencies_descriptive",
      "unassigned"
    ]
  }
</lang-strings>
