<template>
  <div>
    <div class="totara_competency-accordion__header">
      <div>
        <a href="#" @click.prevent="expandAll">Expand all</a> |
        <a href="#" @click.prevent="collapseAll">Collapse all</a>
      </div>
    </div>
    <div class="totara_competency-accordion__items">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      items: [],
    };
  },

  created: function() {
    this.items = this.$children;
  },

  methods: {
    expandAll: function() {
      this.items.forEach(function(accordionItem) {
        accordionItem.open = true;
      });
    },
    collapseAll: function() {
      this.items.forEach(function(accordionItem) {
        accordionItem.open = false;
      });
    },
  },
};
</script>
<style lang="scss">
.totara_competency-accordion__items {
  & > div:not(:last-child) {
    padding-bottom: 5px;
  }
}

.totara_competency-accordion__header {
  display: flex;
  flex-direction: row-reverse;
  padding: 15px 0;
}
</style>
