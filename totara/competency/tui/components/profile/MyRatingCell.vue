<template>
  <div
    class="tui-MyRatingCell__inline"
    @mouseover="showTooltip = true"
    @mouseleave="showTooltip = false"
  >
    <span class="tui-MyRatingCell__link-alike">{{ value.name }}</span>
    <ScaleTooltip v-if="scale" :scale="scale" :display="showTooltip" />
  </div>
</template>

<script>
import ScaleTooltip from 'totara_competency/components/ScaleTooltip';

export default {
  components: { ScaleTooltip },
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
  },
};
</script>
<style lang="scss">
.tui-MyRatingCell__ {
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
