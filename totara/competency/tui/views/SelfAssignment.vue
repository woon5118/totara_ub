<template>
  <div>
    <a :href="goBackLink">{{
      $str('back_to_competency_profile', 'totara_competency')
    }}</a>
    <h2>{{ $str('assign_competencies', 'totara_competency') }}</h2>
    <h4>{{ $str('search_competencies_descriptive', 'totara_competency') }}</h4>
    <div v-if="data.total === 0">
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
    <div v-if="data.total > 0">
      <div style="text-align: right; width: 100%;">
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
          <tr v-for="item in allItems" :key="item.id">
            <td>
              <input
                v-model="selectedItems"
                type="checkbox"
                :value="item.id"
                :disabled="isSelfAssigned(item)"
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
              <button @click="nextCursor = data.next_cursor">
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

export default {
  props: {
    goBackLink: {
      required: true,
      type: String,
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
      nextCursor: null,
      allItems: [],
    };
  },

  methods: {
    assign: function() {
      const confirmMsg =
        'You have selected ' +
        this.selectedItems.length +
        ' competencies to assign.\n\nDo you want to continue?';

      let result = confirm(confirmMsg);
      if (result) {
        // TODO
        // Show loading indicator (or disable assign button)
        // send mutation request
        // Show notification about result
        console.log('Create assignments for: ', this.selectedItems);
      }
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

    selectAll: function() {
      this.selectedItems = [];
      if (!this.allSelected) {
        for (let item in this.allItems) {
          if (this.allItems.hasOwnProperty(item)) {
            this.selectedItems.push(this.allItems[item].id);
          }
        }
      }
    },

    getReasonAssigned: function(competency) {
      var groupedAssignments = [];
      if (competency.user_assignments) {
        competency.user_assignments.forEach(function(assignment) {
          var type = assignment.type;
          if (assignment.type === 'admin') {
            if (assignment.user_group_type === 'user') {
              type = 'individual';
            } else {
              type = assignment.user_group_type;
            }
          }

          if (!groupedAssignments.find(item => item === type)) {
            groupedAssignments.push(type);
          }
        });
      }
      return groupedAssignments;
    },
  },

  apollo: {
    data: {
      query: SelfAssignableCompetenciesQuery,
      variables() {
        let encodedCursor = this.nextCursor;

        // On first load use new cursor
        if (encodedCursor === null) {
          let cursor = {
            limit: 5,
            columns: null,
          };
          encodedCursor = window.btoa(JSON.stringify(cursor));
        }

        return {
          user_id: this.userId,
          cursor: encodedCursor,
        };
      },

      update({ totara_competency_self_assignable_competencies: data }) {
        return data;
      },

      result({
        data: { totara_competency_self_assignable_competencies: data },
      }) {
        data = JSON.parse(JSON.stringify(data));
        this.allItems = this.allItems.concat(data.items.slice(0));
      },
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "loadmore"
    ],
    "totara_hierarchy": [
      "assign"
    ],
    "totara_competency": [
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
