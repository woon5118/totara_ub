<template>
  <div v-if="isActive" class="tui-ProfileTab" role="tabpanel">
    <!-- Content -->
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      required: true,
    },
    subtitle: {
      type: String,
      required: true,
    },
    itemKey: {
      type: [String, Number],
      required: true,
    },
    active: {
      type: Boolean,
      default: false,
    },
  },

  data: function() {
    return {
      isActive: false,
    };
  },

  computed: {},

  watch: {
    active(newValue) {
      this.isActive = newValue;
    },
  },

  created: function() {
    this.isActive = this.active;
  },

  mounted: function() {},

  methods: {},
};
</script>
<style lang="scss">
.totara_competency-profile__accordion-item {
  & .totara_competency-profile__accordion-item-header {
    display: inline-flex;
    padding: 15px;
    width: 100%;
    line-height: 15px;
    background-color: #f7f7f7;
    border: 1px #979797 solid;
    cursor: pointer;

    &[data-is-open='open'] {
      border-radius: 5px 5px 0 0;
    }

    &[data-is-open='closed'] {
      border-radius: 5px;
    }
  }

  & .totara_competency-profile__accordion-item-content {
    background-color: #f9f9f9;
    border: 1px #cbcbcb solid;
    border-top: 0;
    border-radius: 0 0 5px 5px;
  }
}

.totara_competency-profile-accordion-item__fade-enter-active,
.totara_competency-profile-accordion-item__fade-leave-active {
  transition: opacity 0.5s;
}
.totara_competency-profile-accordion-item__fade-enter,
.totara_competency-profile-accordion-item__fade-leave-to {
  opacity: 0;
}
</style>
