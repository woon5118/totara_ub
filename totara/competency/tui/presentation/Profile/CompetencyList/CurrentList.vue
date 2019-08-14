<template>
  <List :columns="columns" :data="competencies">
    <template v-slot:column-name="props">
      <div>
        <a
          :href="competencyDetailsLink(props.row)"
          v-text="props.row.competency.fullname"
        />
      </div>
    </template>
    <template v-slot:column-proficient="props">
      <template v-if="props.row.items[0].proficient">
        <FlexIcon icon="check" alt="//TODO add something here" />
      </template>
    </template>
    <template v-slot:column-rating="props">
      <MyRatingCell
        v-if="props.row.items[0].my_value"
        :value="props.row.items[0].my_value"
        :scales="scales"
      />
    </template>
  </List>
</template>

<script>
import List from '../../../container/List';
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import MyRatingCell from './../MyRatingCell';

let columns = [
  {
    key: 'name',
    value: 'competency.fullname',
    title: 'Competency',
    grow: true,
    size: 'md',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'xs',
    alignment: ['center'],
  },
  {
    key: 'rating',
    title: 'Rating',
    size: 'sm',
  },
];

export default {
  components: {
    MyRatingCell,
    FlexIcon,
    List,
  },

  props: {
    competencies: {
      required: true,
      type: Array,
    },
    baseUrl: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
    scales: {
      required: true,
      type: Array,
    },
  },

  computed: {
    columns() {
      return columns;
    },
  },

  methods: {
    competencyDetailsLink(row) {
      let link = `${this.baseUrl}/details/?competency_id=${row.competency.id}`;

      if (!this.isMine) {
        link += `&user_id=${this.userId}`;
      }

      return link;
    },
  },
};
</script>
<style lang="scss">
.tui-ArchivedCompetencyList__ {
  &archived-assignments-list {
    display: flex;
    padding: 0;
    margin: 0;
    list-style: none;
    flex-grow: 1;
    flex-wrap: wrap;

    &-padded {
      margin-left: 2rem;
    }
  }
}
</style>
<lang-strings>
    {
    }
</lang-strings>
