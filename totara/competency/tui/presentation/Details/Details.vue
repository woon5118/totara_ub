<template>
  <div>
    <h5 class="tui-Details__description" v-html="data.competency.description" />
    <Tabs>
      <Tab
        v-for="(item, key) in data.items"
        :key="key"
        :item-key="key"
        :subtitle="
          item.assignment.archived_at
            ? $str('unassigned', 'totara_competency')
            : ''
        "
        :active="key === 0"
        :name="item.assignment.progress_name"
      >
        <ScaleDetail
          :competency-id="competencyId"
          :my-value="item.my_value"
          :assignment="item.assignment"
        />
      </Tab>
    </Tabs>
    <hr />
  </div>
</template>

<script>
import Tab from './Tab';
import Tabs from './Tabs';
import ScaleDetail from './ScaleDetail';

export default {
  components: { ScaleDetail, Tab, Tabs },
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

  computed: {},
};
</script>
<style lang="scss">
.tui-Details__ {
  &description {
    padding-top: $totara_style_spacing_2;
    padding-bottom: $totara_style_spacing_4;
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
