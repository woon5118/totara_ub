<template>
  <div>
    <h2 v-text="$str('perform:manage_activity', 'mod_perform')" />
    <Button
      v-if="canAdd"
      :text="$str('perform:add_activity', 'mod_perform')"
      @click="showCreateModal()"
    />
    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <CreateActivityModal />
    </ModalPresenter>
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
          :column-header="$str('perform:view:status', 'mod_perform')"
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
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import CreateActivityModal from 'mod_perform/components/activity/modal/CreateActivity';

export default {
  components: {
    Button,
    Cell,
    HeaderCell,
    Table,
    ModalPresenter,
    CreateActivityModal,
  },
  props: {
    canAdd: {
      required: true,
      type: Boolean,
    },
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
      modalOpen: false,
    };
  },
  methods: {
    showCreateModal() {
      this.modalOpen = true;
    },
    modalRequestClose() {
      this.modalOpen = false;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "perform:add_activity",
      "perform:manage_activity",
      "perform:view:name",
      "perform:view:status:active",
      "perform:view:status"
    ]
  }
</lang-strings>
