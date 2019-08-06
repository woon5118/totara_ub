<template>
  <div>
    <div>
      <h2 v-text="data.competency.fullname" />
      <h5 v-html="data.competency.description" />
    </div>
    <div>
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
    </div>
    <hr />
  </div>
</template>

<script>
import Tab from './Tab';
import Tabs from './Tabs';
import ScaleDetail from './ScaleDetail';

import CompetencyDetailsQuery from '../../../webapi/ajax/competency_details.graphql';

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
  },

  data: function() {
    return {
      data: {
        competency: {
          fullname: '',
        },
        items: [],
      },
    };
  },

  computed: {},

  apollo: {
    data: {
      query: CompetencyDetailsQuery,
      variables() {
        return {
          user_id: this.userId,
          competency_id: this.competencyId,
        };
      },
      update({
        totara_competency_profile_competency_details: { competency, items },
      }) {
        return { competency, items };
      },
    },
  },
};
</script>
<style lang="scss"></style>
<lang-strings>
  {
    "totara_competency": [
      "unassigned"
    ]
  }
</lang-strings>
