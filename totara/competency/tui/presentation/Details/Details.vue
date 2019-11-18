<template>
  <div>
    <h5 class="tui-Details__description" v-html="data.competency.description" />
    <Tabs>
      <Tab
        v-for="(item, key) in data.items"
        :key="key"
        :item-key="key"
        :subtitle="
          item.assignment.archived_at && item.assignment.type !== 'legacy'
            ? $str('unassigned', 'totara_competency')
            : ''
        "
        :active="key === 0"
        :name="item.assignment.progress_name"
      >
        <Preloader :display="!displayTabContent[key]" />
        <div
          :class="
            displayTabContent[key] ? 'tui-Details__show' : 'tui-Details__hide'
          "
        >
          <ScaleDetail
            :competency-id="competencyId"
            :user-id="userId"
            :my-value="item.my_value"
            :assignment="item.assignment"
            @loaded="setScaleDetailsLoaded(key)"
          />
          <AchievementDisplay
            :user-id="userId"
            :assignment="item.assignment"
            @loaded="setAchievementDisplayLoaded(key)"
          />
        </div>
      </Tab>
    </Tabs>
    <hr />
  </div>
</template>

<script>
import Tab from './Tab';
import Tabs from './Tabs';
import ScaleDetail from './ScaleDetail';
import AchievementDisplay from 'totara_competency/containers/AchievementDisplay';
import Preloader from 'totara_competency/presentation/Preloader';

export default {
  components: { ScaleDetail, AchievementDisplay, Tab, Tabs, Preloader },
  props: {
    userId: {
      required: true,
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
    data: {
      required: true,
      type: Object,
    },
  },

  data: function() {
    return {
      isScaleDetailsLoaded: {},
      isAchievementDisplayLoaded: {},
      displayTabContent: {},
    };
  },

  computed: {},

  methods: {
    /**
     * Returns true if all childitems of the tab are finished loading
     *
     * @param itemKey
     */
    canDisplayTabContent(itemKey) {
      return (
        this.isScaleDetailsLoaded[itemKey] &&
        this.isAchievementDisplayLoaded[itemKey]
      );
    },

    /**
     * Sets loading state of scaleDetails for given item,
     * triggering the display of the content when all items are loaded
     *
     * @param itemKey
     */
    setScaleDetailsLoaded(itemKey) {
      this.$set(this.isScaleDetailsLoaded, itemKey, true);
      this.updateTabContentDisplayToggle(itemKey);
    },

    /**
     * Sets loading state of scaleDetails for given item,
     * triggering the display of the content when all items are loaded
     *
     * @param itemKey
     */
    setAchievementDisplayLoaded(itemKey) {
      this.$set(this.isAchievementDisplayLoaded, itemKey, true);
      this.updateTabContentDisplayToggle(itemKey);
    },

    /**
     * Set display toggle for tab to true if all chil components are loaded
     *
     * @param itemKey
     */
    updateTabContentDisplayToggle(itemKey) {
      this.$set(
        this.displayTabContent,
        itemKey,
        this.canDisplayTabContent(itemKey)
      );
    },
  },
};
</script>
<style lang="scss">
.tui-Details {
  &__description {
    padding-top: var(--tui-gap-2);
    padding-bottom: var(--tui-gap-4);
  }

  &__show {
    display: block;
  }

  &__hide {
    display: none;
  }
}
</style>
<lang-strings>
  {
    "totara_competency": [
      "unassigned"
    ]
  }
</lang-strings>
