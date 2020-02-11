<template>
  <div>
    <h2 v-text="$str('perform:manage_activity', 'mod_perform')" />
    <Button
      :text="$str('create_activity', 'mod_perform')"
      @click="create_activity()"
    />
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
import Button from 'totara_core/components/buttons/Button';
import CreateActivityMutation from '../../webapi/ajax/create_activity.graphql';

export default {
  components: {
    Button,
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
  methods: {
    create_activity: function() {
      console.log(this);
      this.$apollo
        .mutate({
          // Query
          mutation: CreateActivityMutation,
          // Parameters
          variables: {
            name: 'placeholder_name',
          },
        })
        .then(data => {
          if (data.data && data.data.container_perform_create) {
            console.log('successfully created activity');
          }
        })
        .catch(error => {
          // TODO Handle error case
          console.log('error');
          console.error(error);
        });
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "create_activity",
      "perform:manage_activity",
      "perform:view:name",
      "perform:view:status:active",
      "perform:view:status"
    ]
  }
</lang-strings>
