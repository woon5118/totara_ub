<template>
  <div
    class="tui-MyRatingCell__inline"
    @mouseover="showTooltip = true"
    @mouseleave="showTooltip = false"
  >
    <span class="tui-MyRatingCell__link-alike">{{ value.name }}</span
    ><Tooltip :display="showTooltip">
      <template v-if="scale">
        <strong>
          Rating scale
        </strong>
        <table class="tui-MyRatingCell__table">
          <tr
            v-for="scaleValue in scale.values.slice(0).reverse()"
            :key="scaleValue.id"
          >
            <td :data-has-icon="isMinProficientValue(scaleValue)">
              <FlexIcon v-if="isMinProficientValue(scaleValue)" icon="check" />
            </td>
            <td>{{ scaleValue.name }}</td>
          </tr>
        </table>
      </template>
      <template v-else>
        It's a demo, so, perhaps, something went wrong, can't find scale
      </template>
    </Tooltip>
  </div>
</template>

<script>
import Tooltip from 'totara_competency/containers/Tooltip';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
export default {
  components: { FlexIcon, Tooltip },
  props: {
    value: {
      required: true,
      type: Object,
    },
    scales: {
      required: true,
      type: Array,
    },
  },

  data: function() {
    return {
      showTooltip: false,
    };
  },

  computed: {
    scale() {
      return this.scales.find(({ id }) => id === this.value.scale_id);
    },

    minProficientValue() {
      return this.scale.values.find(({ proficient }) => proficient);
    },
  },

  methods: {
    isMinProficientValue(value) {
      let minProficientValue = this.minProficientValue;

      if (!minProficientValue) {
        return false;
      }

      return minProficientValue.id === value.id;
    },
  },
};
</script>
<style lang="scss">
.tui-MyRatingCell__ {
  &table {
    & td {
      padding: 0.3rem 0.5rem;

      &[data-has-icon] {
        color: #3f9852;
      }
    }
  }

  &inline {
    display: inline-block;
  }

  &link-alike {
    text-decoration: underline;
    text-decoration-style: dashed;
    cursor: pointer;
  }
}
</style>
