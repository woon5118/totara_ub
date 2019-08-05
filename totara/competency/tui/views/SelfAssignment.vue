<template>
  <div>
    <a :href="goBackLink">{{
      $str('back_to_competency_profile', 'totara_competency')
    }}</a>
    <h2>{{ $str('search_competencies', 'totara_competency') }}</h2>
    <h4>{{ $str('search_competencies_descriptive', 'totara_competency') }}</h4>
    <div style="text-align: right; width: 100%;">
      <button
        class="tw-selectionBasket__btn tw-selectionBasket__btn_small tw-selectionBasket__btn_prim"
        :disabled="selectedItems.length == 0"
        @click="assign"
      >
        {{ $str('assign', 'totara_hierarchy') }}
      </button>
    </div>
    <div>
      <strong>{{ data.total }} competencies</strong>
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
          <th style="width: 75%">Name</th>
          <th style="width: 20%">Assigned</th>
        </thead>
        <tbody>
          <tr v-for="item in data.items" :key="item.id">
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
            <td v-if="isAssigned(item)">Assigned</td>
            <td v-else>Unassigned</td>
          </tr>
        </tbody>
        <tfoot>
          <tr v-if="data.next_cursor !== ''">
            <td colspan="4">
              <button @click="loadCompetencies()">Load More</button>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    goBackLink: {
      required: true,
      type: String
    },
    userId: {
      required: true,
      type: Number
    }
  },

  data: function() {
    return {
      allSelected: false,
      selectedItems: [],
      data: {
        items: [],
        total: 0,
        next_cursor: null
      }
    };
  },

  computed: {},

  mounted: function() {
    this.loadCompetencies();
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

    selectAll: function() {
      this.selectedItems = [];
      if (!this.allSelected) {
        for (let item in this.data.items) {
          this.selectedItems.push(this.data.items[item].id);
        }
      }
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

    isAssigned: function(competency) {
      return (
        competency.user_assignments && competency.user_assignments.length > 0
      );
    },

    loadCompetencies: function() {
      let encoded_cursor = this.data.next_cursor;
      // On first load use new cursor
      if (this.data.items.length === 0 && encoded_cursor === null) {
        var cursor = {
          limit: 5,
          columns: null
        };
        encoded_cursor = window.btoa(JSON.stringify(cursor));
      }

      // Load the list via GraphQL
      this.$webapi
        .query('totara_competency_self_assignable_competencies', {
          user_id: this.userId,
          cursor: encoded_cursor
        })
        .then(
          function(data) {
            let result = data.totara_competency_self_assignable_competencies;
            this.data.items = this.data.items.concat(result.items);
            this.data.total = result.total;
            this.data.next_cursor = result.next_cursor;
          }.bind(this)
        );
    }
  }
};
</script>

<lang-strings>
    {
        "totara_hierarchy": [
            "assign"
        ],
        "totara_competency": [
            "back_to_competency_profile",
            "search_competencies",
            "search_competencies_descriptive"
        ]
    }
</lang-strings>
