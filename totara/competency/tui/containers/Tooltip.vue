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
      top: calc(0rem - var(--tui-font-size-8));
      left: 50%;
      width: 0;
      height: 0;
      margin-left: calc(0rem - var(--tui-font-size-8));
      border-right: var(--tui-font-size-8) solid transparent;
      border-bottom: var(--tui-font-size-8) solid var(--tui-color-neutral-5);
      border-left: var(--tui-font-size-8) solid transparent;
      content: '';
    }
    position: absolute;
    width: max-content;
    margin-top: var(--tui-font-size-8);
    padding: var(--tui-font-size-8) var(--tui-font-size-12);

    background-color: var(--tui-color-neutral-1);
    border: var(--tui-font-size-1) var(--tui-color-neutral-4) solid;
  }
}
</style>
