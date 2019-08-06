<template>
  <div v-if="!assignment.archived_at" class="tui-ScaleDetail__list">
    <div class="tui-ScaleDetail__row">
      <div class="tui-ScaleDetail__value-cell">
        <h4>Competency scale</h4>
      </div>
      <div
        v-if="!myValue && scale.values"
        class="tui-ScaleDetail__my-value-cell"
      >
        <div data-no-rating="1">No rating 🙁</div>
      </div>
    </div>
    <div
      v-for="value in reversedValues"
      :key="value.key"
      class="tui-ScaleDetail__row"
    >
      <div class="tui-ScaleDetail__value-cell" :data-active="isMyValue(value)">
        <span v-text="value.name" /><br />
        <small v-text="value.description" />
        <span v-if="minProficientValue && minProficientValue.id === value.id">
          <FlexIcon icon="star" /> - Proficient
        </span>
      </div>
      <div v-if="isMyValue(value)" class="tui-ScaleDetail__my-value-cell">
        <div>Your rating <FlexIcon icon="nav-expand" /></div>
      </div>
    </div>
  </div>
  <div v-else>
    <h4
      v-text="
        $str(
          'competency_archived_at',
          'totara_competency',
          assignment.archived_at
        )
      "
    />
    <div class="tui-ScaleDetail__proficient-box">
      <div>Your rating <FlexIcon icon="nav-expand" /></div>
      <div>
        <span v-if="myValue" v-text="myValue.name" />
        <span
          v-else
          v-text="$str('proficiency_not_achieved', 'totara_competency')"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/containers/icons/FlexIcon';

import ScaleDetailsQuery from '../../../webapi/ajax/scale.graphql';

export default {
  components: { FlexIcon },
  props: {
    myValue: {
      required: true,
      validator: prop => typeof prop === 'object' || prop === null,
    },
    assignment: {
      required: true,
      type: Object,
    },
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      scale: {},
    };
  },

  computed: {
    minProficientValue() {
      if (!this.scale.values) return null;

      return this.scale.values.find(({ proficient }) => proficient);
    },

    reversedValues() {
      if (!this.scale.values) return [];
      return this.scale.values.slice(0).reverse();
    },
  },

  apollo: {
    scale: {
      query: ScaleDetailsQuery,
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update({ totara_competency_scale: scale }) {
        return scale;
      },
    },
  },

  methods: {
    isMyValue(value) {
      return this.myValue && this.myValue.id === value.id;
    },
  },
};
</script>
<style lang="scss">
.tui-ScaleDetail__ {
  &list {
    display: flex;
    flex-direction: column;
  }

  &row {
    display: flex;
    flex-direction: row-reverse;
  }

  &value-cell {
    border-bottom: 1px solid #dde1e5;
    max-width: 80%;
    flex-grow: 1;
    padding: 2rem;

    &[data-active] {
      font-weight: bold;
    }
  }

  &my-value-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-grow: 1;
    max-width: 20%;

    background-color: #e1e1e1;

    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center right;
    & > div {
      text-align: center;
      font-weight: bold;

      &:not([data-no-rating]) {
        font-size: 3rem;
      }
      &[data-no-rating] {
        font-size: 1.5rem;
      }
    }
  }

  &proficient-box {
    background-color: #e1e1e1;

    display: inline-flex;
    align-items: center;
    justify-content: space-between;

    padding: 2rem 4rem;

    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center right;
    & > div {
      text-align: center;
      font-weight: bold;

      font-size: 1.5rem;

      &:nth-child(2) {
        margin-left: 5rem;
      }
    }
  }
}
</style>
<lang-strings>
  {
    "totara_competency": [
      "unassigned",
      "competency_archived_at",
      "proficiency_not_achieved"
    ]
  }
</lang-strings>
