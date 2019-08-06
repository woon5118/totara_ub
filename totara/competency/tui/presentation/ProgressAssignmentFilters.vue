<template>
  <div v-if="displayFilters" style="display: inline-flex;">
    <label
      for="totara_competency-profile-assignment-filters-unique-id"
      class="totara_competency-profile-assignment-filters_label"
      v-text="$str('viewing', 'totara_competency')"
    />
    <select
      id="totara_competency-profile-assignment-filters-unique-id"
      :disabled="disable"
      @change="filterUpdated"
    >
      <option
        v-for="(option, key) in filterOptions"
        :key="key"
        :value="key"
        v-text="option.name"
      />
    </select>
  </div>
</template>

<script>
export default {
  props: {
    filters: {
      type: Array,
      required: true,
    },
    value: {
      type: Object,
      required: true,
    },
    disable: {
      type: Boolean,
      default: false,
    },
  },

  data: function() {
    return {
      f: false,
    };
  },

  computed: {
    filterOptions: function() {
      let options = [];

      let types = {};

      this.filters.forEach(function(filter) {
        if (typeof types[filter.status] === 'undefined') {
          types[filter.status] = {
            name: filter.status_name,
            value: {
              status: filter.status,
              type: null,
              user_group_id: null,
              user_group_type: null,
            },
            indent: false,
          };

          options.push(types[filter.status]);
        }

        options.push({
          name: '\xa0\xa0\xa0' + filter.name,
          value: {
            status: filter.status,
            type: filter.type,
            user_group_id: filter.user_group_id,
            user_group_type: filter.user_group_type,
          },
          indent: true,
        });
      });

      return options;
    },

    displayFilters: function() {
      return !(this.filters.length === 1 && this.filters[0].status === 1);
    },
  },

  created: function() {},

  mounted: function() {},

  methods: {
    toggle: function() {
      this.open = !this.open;
    },

    filterUpdated: function(e) {
      this.$emit('input', this.filterOptions[e.target.value].value);
      this.$emit('changed', this.filterOptions[e.target.value].value);
    },
  },
};
</script>
<style lang="scss">
.totara_competency-profile-assignment-filters_label {
  line-height: 32px;
  margin-right: 10px;
}
</style>
<lang-strings>
    {
        "totara_competency": ["viewing"]
    }
</lang-strings>
