<template>
  <div v-if="display" class="tui-Tooltip__wrapper">
    <div class="tui-Tooltip__content">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    display: {
      required: true,
      type: Boolean,
    },
  },

  data: function() {
    return {};
  },

  methods: {},
};
</script>
<style lang="scss">
.tui-Tooltip__ {
  &wrapper {
    position: relative;
    width: 0;
    height: 0;
    overflow: visible;
  }

  &content {
    &:before {
      position: absolute;
      top: -$totara_style-size_8;
      left: 50%;
      width: 0;
      height: 0;
      margin-left: -$totara_style-size_8;
      border-right: $totara_style-size_8 solid transparent;
      border-bottom: $totara_style-size_8 solid $totara_style-color_neutral_5;
      border-left: $totara_style-size_8 solid transparent;
      content: '';
    }
    position: absolute;
    width: max-content;
    margin-top: $totara_style-size_8;
    padding: $totara_style-size_8 $totara_style-size_12;

    background-color: $totara_style-color_neutral_1;
    border: $totara_style-size_1 $totara_style-color_neutral_4 solid;
  }
}
</style>
