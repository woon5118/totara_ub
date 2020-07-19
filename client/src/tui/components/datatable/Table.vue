<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
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
    <HeaderRow :empty="!$slots['header-row']">
      <HeaderCell
        v-if="$slots['header-row'] && draggableRows"
        class="tui-dataTable__row-move-cell"
      />
      <slot name="header-row" />
    </HeaderRow>

    <PreRows v-if="this.$slots['pre-rows']">
      <slot name="pre-rows" />
    </PreRows>

    <render :vnode="draggableDropTarget" />

    <RowGroup
      v-for="{ group, groupId, rows, expandGroup } in rowGroupData"
      :key="groupId"
      :selected="isSelected(groupId)"
      :wrap="groupMode"
    >
      <template v-for="({ row, id, expand, index }, groupIndex) in rows">
        <!--
          Workarounds for Vue limitations:
            * It is difficult to conditionally wrap a component when using
              template syntax (it is easier with JSX), so we need to do a trick
              using <component :is> and a special "passthrough" component that
              just renders its children.
            * Vue can't bind native listeners with v-on="object" syntax, so we
              need to use PropsProvider to enable that - which results in another
              <component :is> passthrough. This is fixed in Vue 3.
        -->
        <component
          :is="draggableRows ? 'Draggable' : 'passthrough'"
          :key="id"
          v-slot="{
            dragging,
            attrs,
            nativeEvents,
            moveMenu,
          }"
          :type="getDraggableType(row)"
          :value="getDraggableValue(row)"
          :index="index + indexOffset"
          :renderless="true"
        >
          <component
            :is="draggableRows ? 'PropsProvider' : 'passthrough'"
            :key="id"
            :provide="{ nativeListeners: nativeEvents }"
          >
            <Row
              :key="id"
              :border-bottom-hidden="borderBottomHidden"
              :border-separator-hidden="borderSeparatorHidden"
              :border-top-hidden="borderTopHidden"
              :disabled="isDisabled(id)"
              :hover-off="hoverOff"
              :in-group="groupMode"
              :selected="isSelected(id)"
              :color-odd="colorOddRows && !draggableRows"
              :draggable="draggableRows"
              :dragging="dragging"
              v-bind="attrs"
            >
              <Cell
                v-if="draggableRows"
                class="tui-dataTable__row-move-cell"
                valign="center"
              >
                <DragHandleIcon />
                <div v-if="draggableRows" class="tui-dataTable__row-move-menu">
                  <render :vnode="moveMenu" />
                </div>
              </Cell>
              <slot
                :id="id"
                name="row"
                :expand="expand"
                :expandState="expandableRows && id == expanded"
                :expand-group="expandGroup"
                :first-in-group="groupIndex === 0"
                :group-id="groupId"
                :in-group="groupMode"
                :row="row"
                :dragging="dragging"
              />
            </Row>
          </component>
        </component>

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

    <render :vnode="draggablePlaceholder" />
  </div>
</template>

<script>
import ExpandedRow from 'tui/components/datatable/ExpandedRow';
import HeaderRow from 'tui/components/datatable/HeaderRow';
import PreRows from 'tui/components/datatable/PreRows';
import Row from 'tui/components/datatable/Row';
import RowGroup from 'tui/components/datatable/RowGroup';
import Draggable from 'tui/components/drag_drop/Draggable';
import PropsProvider from 'tui/components/util/PropsProvider';
import Cell from 'tui/components/datatable/Cell';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import DragHandleIcon from 'tui/components/icons/common/DragHandle';

export default {
  components: {
    ExpandedRow,
    HeaderRow,
    PreRows,
    Row,
    RowGroup,
    Draggable,
    PropsProvider,
    Cell,
    HeaderCell,
    DragHandleIcon,
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

    // draggable:
    draggableRows: Boolean,
    draggablePlaceholder: Object,
    draggableDropTarget: Object,
    indexOffset: {
      type: Number,
      default: 0,
    },
    draggableValue: {
      type: Function,
    },
    draggableType: {
      type: [String, Function],
    },
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
          index,
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
      if (!this.groupMode || this.draggableRows) {
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

    /**
     * Get the value to use for a draggable row.
     */
    getDraggableValue(row) {
      return typeof this.draggableValue === 'function'
        ? this.draggableValue(row)
        : row;
    },

    /**
     * Get the type to use for a draggable row.
     */
    getDraggableType(row) {
      if (!this.draggableRows) {
        return;
      }

      if (typeof this.draggableType === 'string') {
        return this.draggableType;
      } else if (typeof this.draggableType === 'function') {
        return this.draggableType(row);
      }
      console.error(
        'draggable-type prop must be supplied to Table when draggable-rows is true.'
      );
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": ["noitems"]
  }
</lang-strings>
