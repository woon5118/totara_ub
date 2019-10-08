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
        <Preloader :display="!displayToggle[key]" />
        <div
          :class="
            displayToggle[key] && displayToggle[key] === true
              ? 'tui-Details__show'
              : 'tui-Details__hide'
          "
        >
          <ScaleDetail
            :competency-id="competencyId"
            :user-id="userId"
            :my-value="item.my_value"
            :assignment="item.assignment"
            @loaded="scaleDetailsLoaded(key)"
          />
          <AchievementDisplay
            :user-id="userId"
            :assignment="item.assignment"
            @loaded="achievementDisplayLoaded(key)"
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
      loadedScaleDetails: {},
      loadedAchievementDisplay: {},
      displayToggle: {},
    };
  },

  computed: {},

  methods: {
    canDisplay(itemKey) {
      return (
        this.loadedScaleDetails[itemKey] &&
        this.loadedAchievementDisplay[itemKey]
      );
    },
    scaleDetailsLoaded(itemKey) {
      this.$set(this.loadedScaleDetails, itemKey, true);
      this.updateDisplayToggle(itemKey);
    },
    achievementDisplayLoaded(itemKey) {
      this.$set(this.loadedAchievementDisplay, itemKey, true);
      this.updateDisplayToggle(itemKey);
    },
    updateDisplayToggle(itemKey) {
      this.$set(this.displayToggle, itemKey, this.canDisplay(itemKey));
    },
  },
};
</script>
<style lang="scss">
.tui-Details {
  &__description {
    padding-top: $totara_style_spacing_2;
    padding-bottom: $totara_style_spacing_4;
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
