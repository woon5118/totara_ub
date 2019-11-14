<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div v-if="!data.length">
    {{ noItemsText }}
  </div>
  <div
    v-else
    class="tui-dataTable"
    :class="{
      'tui-dataTable--archived': archived,
    }"
    role="table"
  >
    <HeaderRow :empty="!this.$slots['header-row']">
      <slot name="header-row" />
    </HeaderRow>

    <PreRows v-if="this.$slots['pre-rows']">
      <slot name="pre-rows" />
    </PreRows>

    <RowGroup
      v-for="{ group, groupId, rows, expandGroup } in rowGroupData"
      :key="groupId"
      :selected="isSelected(groupId)"
      :wrap="groupMode"
    >
      <template v-for="({ row, id, expand }, index) in rows">
        <Row
          :key="id"
          :border-bottom-hidden="borderBottomHidden"
          :border-separator-hidden="borderSeparatorHidden"
          :border-top-hidden="borderTopHidden"
          :disabled="isDisabled(id)"
          :hover-off="hoverOff"
          :in-group="groupMode"
          :selected="isSelected(id)"
          :color-odd="colorOddRows"
        >
          <slot
            :id="id"
            :expand="expand"
            :expandState="expandableRows && id == expanded"
            :expand-group="expandGroup"
            :first-in-group="index === 0"
            :group-id="groupId"
            :in-group="groupMode"
            name="row"
            :row="row"
          />
        </Row>

        <ExpandedRow
          v-if="expandableRows && id == expanded"
          :key="id + ' expand'"
          @close="updateExpandedRow()"
        >
          <slot name="expand-content" :row="row" />
        </ExpandedRow>
      </template>

      <ExpandedRow
        v-if="expandableRows && groupMode && groupId == expandedGroup"
        :key="groupId + ' expand'"
        @close="updateExpandedGroup()"
      >
        <slot name="group-expand-content" :group="group" />
      </ExpandedRow>
    </RowGroup>
  </div>
</template>

<script>
import ExpandedRow from 'totara_core/components/datatable/ExpandedRow';
import HeaderRow from 'totara_core/components/datatable/HeaderRow';
import PreRows from 'totara_core/components/datatable/PreRows';
import Row from 'totara_core/components/datatable/Row';
import RowGroup from 'totara_core/components/datatable/RowGroup';

export default {
  components: {
    ExpandedRow,
    HeaderRow,
    PreRows,
    Row,
    RowGroup,
  },

  props: {
    // Table is displaying archived content
    archived: Boolean,
    // Hide last border bottom
    borderBottomHidden: Boolean,
    // Hide separator border between rows
    borderSeparatorHidden: Boolean,
    // Hide first border top
    borderTopHidden: Boolean,
    // Enable background colour on odd rows
    colorOddRows: Boolean,
    data: Array,
    // List of disabled IDs
    disabledIds: Array,
    // No hover background for rows
    hoverOff: Boolean,
    // The text to display if the data array is empty
    noItemsText: {
      type: String,
      default() {
        return this.$str('noitems', 'totara_core');
      },
    },
    // Entire result set selection state
    entireSelected: Boolean,
    // Enables the ability to have expandable rows
    expandableRows: Boolean,
    getGroupId: {
      type: Function,
      default: (group, index) => ('id' in group ? group.id : index),
    },
    getId: {
      type: Function,
      default: (row, index) => ('id' in row ? row.id : index),
    },
    // Enables group mode
    groupMode: Boolean,
    // ID's of selected rows
    selection: Array,
  },

  data() {
    return {
      expanded: null,
      expandedGroup: null,
    };
  },

  computed: {
    /**
     * Return row data
     *
     * @return {Array}
     */
    rowData() {
      if (!Array.isArray(this.data)) {
        return [];
      }
      return this.data.map((row, index) => {
        const id = this.getId(row, index);
        return {
          row,
          id,
          expand: () => this.updateExpandedRow(id),
        };
      });
    },

    /**
     * Return row data based on grouping
     *
     * @return {Array}
     */
    rowGroupData() {
      if (!this.groupMode) {
        return [{ id: null, rows: this.rowData }];
      }

      return this.data.map((group, groupIndex) => {
        const groupId = this.getGroupId(group, groupIndex);
        return {
          groupId,
          group,
          expandGroup: () => this.updateExpandedGroup(groupId),
          rows: group.rows.map((row, rowIndex) => {
            const id = this.getId(row, groupId + ':' + rowIndex);
            return {
              row,
              id,
              expand: () => this.updateExpandedRow(id),
            };
          }),
        };
      });
    },
  },

  methods: {
    /**
     * Check if row has been disabled
     *
     * @param {Int} id
     */
    isDisabled(id) {
      return this.disabledIds && this.disabledIds.includes(id);
    },

    /**
     * Check if row has been selected
     *
     * @param {Int} id
     */
    isSelected(id) {
      return (
        this.selection && (this.entireSelected || this.selection.includes(id))
      );
    },

    /**
     * set expanded to ID of expanded row
     *
     */
    updateExpandedRow(rowId) {
      this.expandedGroup = null;
      this.expanded =
        rowId === undefined || this.expanded === rowId ? null : rowId;
    },

    /**
     * set expanded to ID of expanded row group
     *
     */
    updateExpandedGroup(groupId) {
      this.expanded = null;
      const expand = this.expandedGroup !== groupId;
      this.expandedGroup = groupId !== undefined && expand ? groupId : null;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": ["noitems"]
  }
</lang-strings>
