<template>
  <div>
    <div>
      <h2 v-text="competency.fullname"></h2>
      <h5 v-html="competency.description"></h5>
    </div>
    <div>
      <Tabs>
        <Tab
          v-for="(item, key) in assignmentItems"
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
          ></ScaleDetail>
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

export default {
  components: { ScaleDetail, Tab, Tabs },
  props: {
    userId: {
      required: true,
      type: Number
    },
    competencyId: {
      required: true,
      type: Number
    }
  },

  data: function() {
    return {
      competency: {
        fullname: ''
      },
      assignmentItems: []
    };
  },

  computed: {},

  mounted: function() {
    // Fetch competency details
    this.$webapi
      .query('totara_competency_competency_details', {
        user_id: this.userId,
        competency_id: this.competencyId
      })
      .then(
        ({
          totara_competency_profile_competency_details: { competency, items }
        }) => {
          this.competency = competency;
          this.assignmentItems = items;
        }
      );
  },

  methods: {
    fetchCompetencyDetails() {}
  }
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
