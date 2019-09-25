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
      v-if="!isLegacy"
      v-text="
        $str(
          'assignment_archived_at',
          'totara_competency',
          assignment.archived_at
        )
      "
    />
    <div class="tui-ScaleDetail__proficient-box">
      <div>Your rating <FlexIcon icon="nav-expand" /></div>
      <div>
        <template v-if="isLegacy">
          <span
            v-if="myValue.proficient"
            v-text="
              $str('proficient_on', 'totara_competency', assignment.created_at)
            "
          />
          <span
            v-else
            v-text="$str('proficiency_not_achieved', 'totara_competency')"
          />
        </template>
        <template v-else>
          <span v-if="myValue" v-text="myValue.name" />
          <span
            v-else
            v-text="$str('proficiency_not_achieved', 'totara_competency')"
          />
        </template>
      </div>
    </div>
    <template v-if="myValue && isLegacy">
      <div
        v-text="
          $str('legacy_assignment_rating_discontinued', 'totara_competency')
        "
      />
      <div
        v-text="
          $str('legacy_assignment_rating_description', 'totara_competency')
        "
      />
    </template>
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

    isLegacy() {
      return this.assignment.type === 'legacy';
    },

    reversedValues() {
      if (!this.scale.values) return [];
      return this.scale.values.slice(0).reverse();
    },
  },

  apollo: {
    scale: {
      query: ScaleDetailsQuery,
      context: { batch: true },
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
    flex-grow: 1;
    max-width: 80%;
    padding: 2rem;
    border-bottom: 1px solid #dde1e5;

    &[data-active] {
      font-weight: bold;
    }
  }

  &my-value-cell {
    display: flex;
    flex-grow: 1;
    align-items: center;
    justify-content: center;
    max-width: 20%;

    background-color: #e1e1e1;

    background-repeat: no-repeat;
    background-position: center right;
    background-attachment: fixed;
    & > div {
      font-weight: bold;
      text-align: center;

      &:not([data-no-rating]) {
        font-size: 3rem;
      }
      &[data-no-rating] {
        font-size: 1.5rem;
      }
    }
  }

  &proficient-box {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;

    padding: 2rem 4rem;
    background-color: #e1e1e1;

    background-repeat: no-repeat;
    background-position: center right;
    background-attachment: fixed;
    & > div {
      font-weight: bold;

      font-size: 1.5rem;
      text-align: center;

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
      "assignment_archived_at",
      "proficient_on",
      "proficiency_not_achieved",
      "legacy_assignment_rating_discontinued",
      "legacy_assignment_rating_description"
    ]
  }
</lang-strings>
