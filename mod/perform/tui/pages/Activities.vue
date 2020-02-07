<template>
  <div>
    <h2 v-text="$str('perform:manage', 'mod_perform')" />
    <Table :data="activities" :expandable-rows="true">
      <template v-slot:header-row>
        <HeaderCell size="8">{{
          $str('perform:view:name', 'mod_perform')
        }}</HeaderCell>
        <HeaderCell size="2">{{
          $str('perform:view:status', 'mod_perform')
        }}</HeaderCell>
      </template>
      <template v-slot:row="{ row, expand }">
        <Cell
          size="8"
          :column-header="$str('perform:view:name', 'mod_perform')"
        >
          {{ row.name }}
        </Cell>
        <Cell
          size="2"
          :column-header="$str('perform:view:date', 'mod_perform')"
        >
          {{ $str('perform:view:status:active', 'mod_perform') }}
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
import Table from 'totara_core/components/datatable/Table';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import performActivitiesQuery from '../../webapi/ajax/activities.graphql';

export default {
  components: {
    Cell,
    HeaderCell,
    Table,
  },
  apollo: {
    activities: {
      query: performActivitiesQuery,
      variables() {
        return [];
      },
      update: data => data.mod_perform_activities,
    },
  },
  data() {
    return {
      activities: [],
    };
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
        "perform:manage",
        "perform:view:name",
        "perform:view:status:active",
        "perform:view:status"
    ]
  }
</lang-strings>
