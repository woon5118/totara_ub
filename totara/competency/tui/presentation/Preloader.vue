<template>
  <transition name="tui-Preloader__transition-fade">
    <div v-if="display" class="tui-Preloader">
      <div>
        <h1 class="title is-1" v-text="$str('loading', 'totara_competency')" />
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    display: {
      required: false,
      type: Boolean,
      default: true,
    },
  },
};
</script>
<style lang="scss">
.tui-Preloader {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.6);
  z-index: 100;

  // Fade transitions
  &__transition-fade {
    &-enter-active,
    &-leave-active {
      transition: opacity 0.5s;
    }

    &-enter,
    &-leave-to {
      opacity: 0;
    }
  }
}
</style>
<lang-strings>
  {
  "totara_competency": ["loading"]
  }
</lang-strings>
