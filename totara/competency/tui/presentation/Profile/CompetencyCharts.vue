<template>
  <div>
    <template v-if="data.items && data.items.length === 1">
      <IndividualAssignmentProgress :assignment-progress="data.items[0]" />
    </template>
    <template v-else>
      <Accordion ref="assignmentsProgressAccordion">
        <AccordionItem
          v-for="(item, key) in data.items"
          :key="key"
          :name="item.name"
          :item-key="key"
          :is-open="data.items.length <= 5"
        >
          <IndividualAssignmentProgress :assignment-progress="item" />
        </AccordionItem>
      </Accordion>
    </template>
  </div>
</template>

<script>
import Accordion from '../../container/Accordion';
import AccordionItem from '../../container/AccordionItem';
import IndividualAssignmentProgress from '../../container/IndividualAssignmentProgress';

export default {
  components: {
    IndividualAssignmentProgress,
    AccordionItem,
    Accordion,
  },

  props: {
    data: {
      required: true,
      type: Object,
    },
  },

  data: function() {
    return {};
  },

  computed: {},

  watch: {},

  mounted: function() {},

  methods: {},
};
</script>
<style lang="scss"></style>
<lang-strings>
  {}
</lang-strings>
