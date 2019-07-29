<template>
  <div>
    <div class="tui-ProfileTabs__tabs-header">
      <ul role="tablist">
        <li
          v-for="tab in tabs"
          :key="tab.itemKey"
          :class="{ 'tui-ProfileTabs__tabs-active': isActiveTab(tab) }"
          @click.prevent="selectTab(tab)"
        >
          <div>
            <strong v-text="tab.name"></strong>
          </div>
          <div v-if="tab.subtitle">
            <small v-text="tab.subtitle"></small>
          </div>
        </li>
      </ul>
    </div>
    <div class="tui-ProfileTabs__tabs">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tabs: []
    };
  },

  created() {
    this.tabs = this.$children;
  },

  mounted() {
    //this.activateFirst(); // Doesn't work :( // Tabs are still empty there
  },

  methods: {
    isActiveTab(tab) {
      return tab.isActive;
    },

    selectTab(selectedTab) {
      this.tabs.forEach(tab => {
        tab.isActive = tab.name === selectedTab.name;
      });
    },

    activateFirst() {
      let activeTab = null;
      this.tabs.forEach(tab => {
        console.log('1', tab);
        if (tab.isActive) {
          activeTab = tab;
        }
      });

      if (!activeTab && this.tabs[0]) {
        this.tabs[0].$data.isActive = true;
      }
    }
  }
};
</script>
<style lang="scss">
.tui-ProfileTabs__ {
  &tabs {
    &-header {
      & > ul {
        display: flex;
        padding: 0;
        margin: 0;
        list-style: none;
        flex-grow: 1;
        flex-wrap: wrap;
        text-align: left;

        & > li {
          padding: 1.5rem 4.5rem;

          &:not(.tui-ProfileTabs__tabs-active) {
            border-left: 1px transparent solid;
            border-right: 1px transparent solid;
            border-top: 1px transparent solid;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
          }
        }
      }

      border-bottom: 1px black solid;
    }

    &-active {
      font-weight: bold;
      border-left: 1px #00a5e3 solid;
      border-right: 1px #00a5e3 solid;
      border-top: 1px #00a5e3 solid;
      border-top-left-radius: 3px;
      border-top-right-radius: 3px;
    }
  }
}

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
