<template>
  <div :class="getBgStyle">
    <div class="totara_competency-list__row totara_competency-list__header">
      <!-- Header -->
      <div
        v-for="(column, index) in columns"
        :key="getColumnKey(column, index)"
        :class="getColumnClasses(column)"
        class="totara_competency-list__cell totara_competency-list__cell-header"
      >
        <slot
          :name="'column-title-' + getColumnKey(column, index)"
          :column="column"
        >
          <div>
            <strong v-text="getColumnTitle(column)"></strong>
          </div>
        </slot>
      </div>
    </div>
    <div
      class="totara_competency-list__rows totara_competency-list__rows-divided"
    >
      <!-- Content -->
      <div
        v-for="(row, index) in getData"
        :key="getRowKey(row, index)"
        class="totara_competency-list__row"
      >
        <div
          v-for="(column, columnIndex) in columns"
          :key="getColumnKey(column, columnIndex)"
          :class="getColumnClasses(column)"
          class="totara_competency-list__cell"
        >
          <slot
            :name="'column-' + getColumnKey(column, columnIndex)"
            :column="column"
            :row="row"
          >
            <div v-text="getColumnValue(column, row)"></div>
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Array,
      required: true
    },

    columns: {
      type: Array,
      required: true
    },

    rowKey: {
      type: [String, Function],
      required: false,
      default: null
    },

    bgColor: {
      type: String,
      required: false,
      default: ''
    }
  },

  data: function() {
    return {
      items: []
    };
  },

  computed: {
    getData: function() {
      // TODO support passing paginated data
      return this.data;
    },

    getBgStyle() {
      if (this.bgColor.trim() !== '') {
        return ['tui-List__background-gray'];
      }

      return [];
    }
  },

  methods: {
    getRowKey: function(row, index) {
      switch (typeof this.rowKey) {
        case 'function':
          return this.rowKey(row, index);

        case 'string':
          if (typeof row[this.rowKey] !== 'undefined') {
            return row[this.rowKey];
          }
      }

      return index;
    },

    getColumnKey: function(column, index) {
      if (typeof column.key !== 'undefined') {
        return column.key;
      }

      return index;
    },

    getColumnTitle: function(column) {
      if (typeof column.title !== 'undefined') {
        return column.title;
      }

      return '';
    },

    getColumnValue: function(column, row) {
      const value = column.value;

      switch (typeof value) {
        case 'string': {
          let obj = row;

          value.split('.').forEach(function(key) {
            if (typeof obj[key] !== 'undefined') {
              obj = obj[key];
            } else {
              return '';
            }
          });

          return obj;
        }

        case 'function':
          return value(row, column);

        default:
          return '';
      }
    },

    getColumnSize: function(column) {
      if (typeof column.size === 'string') {
        return {
          ['totara_competency-list__cell-size-' + column.size]: true
        };
      }
    },

    getColumnAlignment: function(column) {
      let alignment = column.alignment;

      if (typeof alignment !== 'undefined') {
        if (!Array.isArray(alignment)) {
          alignment = [alignment];
        }

        let result = {};

        alignment.forEach(item => {
          if (typeof item === 'string') {
            result[`totara_competency-list__cell-align-${item}`] = true;
          }
        });
        return result;
      }
    },

    getColumnClasses: function(column) {
      let classes = {
        'totara_competency-list__cell-grow':
          typeof column.grow === 'boolean' && column.grow === true
      };

      return Object.assign(
        classes,
        this.getColumnSize(column),
        this.getColumnAlignment(column)
      );
    }
  }
};
</script>
<style lang="scss">
.tui-List__ {
  &background-gray {
    background-color: #f3f3f3;
  }
}

.totara_competency-list__cell {
  padding: 1.5rem;

  // Header is bold bt default
  &.totara_competency-list__cell-header {
    font-weight: bold;
  }

  // These sizes are arbitrary
  &.totara_competency-list__cell-size-xs {
    flex-basis: 10%;
  }

  &.totara_competency-list__cell-size-sm {
    flex-basis: 20%;
  }

  &.totara_competency-list__cell-size-md {
    flex-basis: 30%;
  }

  &.totara_competency-list__cell-size-lg {
    flex-basis: 50%;
  }

  &.totara_competency-list__cell-grow {
    flex-grow: 1;
  }

  // Content alignment classes for cells
  &.totara_competency-list__cell-align-center {
    justify-content: center;
    text-align: center;
  }

  &.totara_competency-list__cell-align-left {
    justify-content: left;
    text-align: left;
  }
}

.totara_competency-list__rows {
  &.totara_competency-list__rows-striped {
    & .totara_competency-list__row:nth-child(odd) {
      background-color: rgba(0, 0, 0, 0.05);
    }
  }

  &.totara_competency-list__rows-divided {
    & .totara_competency-list__row:not(:last-child) {
      border-bottom: 1px solid #a8b7c7;
    }
  }
}

.totara_competency-list__row {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  width: 100%;
  justify-content: space-between;

  &.totara_competency-list__header {
    border-bottom: 2px black solid;
  }
}
</style>
