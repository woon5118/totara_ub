<template>
  <div class="totara_competency-profile__accordion-item">
    <div
      class="totara_competency-profile__accordion-item-header"
      :data-is-open="open ? 'open' : 'closed'"
      @click.prevent="toggle"
    >
      <!-- Header -->
      <div>
        <FlexIcon :id="toggleIcon"></FlexIcon>
      </div>
      <div style="padding-left: 7px;">
        <strong>
          {{ name }}
        </strong>
      </div>
    </div>
    <transition name="totara_competency-profile-accordion-item__fade">
      <div
        v-if="open"
        class="totara_competency-profile__accordion-item-content"
      >
        <!-- Content -->
        <slot></slot>
      </div>
    </transition>
  </div>
</template>

<script>
import FlexIcon from '../../../core/tui/presentation/icons/FlexIcon';
export default {
  components: { FlexIcon },
  props: {
    name: {
      type: String,
      required: true
    },
    itemKey: {
      type: [String, Number],
      required: true
    },
    isOpen: {
      type: Boolean,
      default: false
    }
  },

  data: function() {
    return {
      open: false
    };
  },

  computed: {
    toggleIcon: function() {
      return this.open
        ? 'totara_core|accordion-expanded'
        : 'totara_core|accordion-collapsed';
    }
  },

  watch: {
    isOpen: function(newValue) {
      this.open = newValue;
    }
  },

  created: function() {
    this.open = this.isOpen;
  },

  mounted: function() {},

  methods: {
    toggle: function() {
      this.open = !this.open;
    }
  }
};
</script>
<style lang="scss">
.totara_competency-profile__accordion-item {
  & .totara_competency-profile__accordion-item-header {
    display: inline-flex;
    padding: 15px;
    width: 100%;
    line-height: 15px;
    background-color: #fff;
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
    background-color: #fff;
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
