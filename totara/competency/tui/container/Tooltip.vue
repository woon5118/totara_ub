<template>
  <div v-if="display" class="tui-Tooltip__wrapper">
    <div class="tui-Tooltip__content">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    display: {
      required: true,
      type: Boolean
    }
  },

  data: function() {
    return {};
  },

  methods: {}
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
      content: '';
      position: absolute;
      top: -10px;
      left: 50%;
      margin-left: -10px;
      width: 0;
      height: 0;
      border-left: 10px solid transparent;
      border-right: 10px solid transparent;
      border-bottom: 10px solid black;
    }
    margin-top: 10px;
    position: absolute;
    padding: 2rem;
    border: 1px #000 solid;
    width: 300px;
    border-radius: 1rem;

    background-color: #fff;
  }
}
</style>
