<template>
  <List :columns="columns" :data="competencies" bg-color="gray">
    <template v-slot:column-name="props">
      <div>
        <a
          :href="competencyDetailsLink(props.row)"
          v-text="props.row.competency.fullname"
        />
      </div>
      <ul
        class="tui-ArchivedCompetencyList__assignments-list tui-ArchivedCompetencyList__assignments-list-padded"
      >
        <li v-for="(item, key) in props.row.items" :key="key">
          <span v-text="item.assignment.progress_name" />
        </li>
      </ul>
    </template>
    <template v-slot:column-archived-date="props">
      <ul class="tui-ArchivedCompetencyList__assignments-list">
        <li v-for="(item, key) in props.row.items" :key="key">
          <span v-text="item.assignment.archived_at" />
        </li>
      </ul>
    </template>
    <template v-slot:column-proficient="props">
      <ul class="tui-ArchivedCompetencyList__assignments-list">
        <li v-for="(item, key) in props.row.items" :key="key">
          <template v-if="item.proficient">
            <FlexIcon icon="check" alt="//TODO add something here" />
          </template>
        </li>
      </ul>
    </template>
    <template v-slot:column-rating="props">
      <ul class="tui-ArchivedCompetencyList__assignments-list">
        <li v-for="(item, key) in props.row.items" :key="key">
          <MyRatingCell
            v-if="item.my_value"
            :value="item.my_value"
            :scales="scales"
          />
        </li>
      </ul>
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
    key: 'archived-date',
    title: 'Archived date',
    size: 'sm',
  },
  {
    key: 'proficient',
    title: 'Proficient',
    size: 'xs',
    alignment: 'center',
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
    flex-grow: 1;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;

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
