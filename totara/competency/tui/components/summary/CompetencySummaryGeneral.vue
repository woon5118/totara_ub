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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <div v-if="!$apollo.loading" class="tui-competencySummaryGeneral">
    <div class="tui-competencySummary__sectionHeader">
      <h3 class="tui-competencySummary__sectionHeader-title">
        {{ $str('general', 'moodle') }}
      </h3>
      <a
        :href="editUrl"
        class="tui-competencySummary__sectionHeader-edit"
        :title="$str('edit', 'moodle')"
      >
        <EditIcon :size="200" :alt="$str('edit', 'moodle')" />
      </a>
    </div>

    <Table
      :data="rows"
      :border-bottom-hidden="true"
      :border-top-hidden="true"
      :border-separator-hidden="true"
      :hover-off="true"
    >
      <template v-slot:row="{ row }">
        <Cell size="6" :heavy="true">
          <div
            class="tui-competencySummaryGeneral__list-label"
            role="rowheader"
          >
            {{ row.label }}
          </div>
        </Cell>

        <Cell size="10">
          <div class="tui-competencySummaryGeneral__list-value" role="cell">
            <template v-if="row.values">
              <div v-for="(item, index) in row.values" :key="'general' + index">
                {{ item }}
              </div>
            </template>
            <div v-else v-html="row.value" />
          </div>
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
// Components
import Cell from 'totara_core/components/datatable/Cell';
import EditIcon from 'totara_core/components/icons/common/Edit';
import Table from 'totara_core/components/datatable/Table';
// Queries
import competencyQuery from 'totara_competency/graphql/competency';

export default {
  components: {
    Cell,
    EditIcon,
    Table,
  },

  props: {
    competencyId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      competency: {},
      editUrl: '',
    };
  },

  computed: {
    /**
     * Get data for each row
     *
     * @return {Array}
     */
    rows() {
      let rows = [
        {
          label: this.$str('fullname', 'totara_competency'),
          value: this.competency.display_name,
        },
        {
          label: this.$str('idnumber', 'totara_competency'),
          value: this.competency.idnumber,
        },
        {
          label: this.$str('description', 'totara_competency'),
          value: this.competency.description,
        },
        {
          label: this.$str('type', 'totara_competency'),
          value: this.competency.type
            ? this.competency.type.display_name
            : false,
        },
        {
          label: this.$str('aggregationmethod', 'totara_hierarchy'),
          value: this.aggregationMethodName,
        },
        {
          label: this.$str(
            'assignment_creation_availability',
            'totara_competency'
          ),
          values: this.assignAvailabilityNames,
          value: false,
        },
      ];

      // Add custom fields (if any)
      if (this.competency.display_custom_fields) {
        this.competency.display_custom_fields.forEach(item => {
          rows.push({
            label: item.title,
            value: item.value,
          });
        });
      }

      rows = rows.filter(function(row) {
        return row.value || row.values;
      });

      return rows;
    },

    assignAvailabilityNames() {
      if (
        !this.competency.assign_availability ||
        !this.competency.assign_availability.length > 0
      ) {
        return false;
      }

      let assignments = [];

      //ASSIGNMENT_CREATE_SELF = 1
      //ASSIGNMENT_CREATE_OTHER = 2;
      this.competency.assign_availability.forEach(item => {
        assignments.push(
          this.$str(
            item === 1
              ? 'competency_assign_availability_self_simple'
              : 'competency_assign_availability_other_simple',
            'totara_competency'
          )
        );
      });

      return assignments;
    },

    aggregationMethodName() {
      if (!this.competency.aggregation_method) {
        return false;
      }

      let aggregationString =
        this.competency.aggregation_method === 1
          ? 'all'
          : this.competency.aggregation_method === 2
          ? 'any'
          : 'off';

      return this.$str(aggregationString, 'totara_competency');
    },
  },

  apollo: {
    competency: {
      query: competencyQuery,
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update({ totara_competency_competency: data }) {
        this.editUrl = this.$url('/totara/hierarchy/item/edit.php', {
          prefix: 'competency',
          id: data.id,
        });
        return data;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "edit",
      "general"
    ],
    "totara_competency": [
      "all",
      "any",
      "assignment_creation_availability",
      "competency_assign_availability_self_simple",
      "competency_assign_availability_other_simple",
      "description",
      "fullname",
      "idnumber",
      "off",
      "type"
    ],
    "totara_hierarchy": [
      "aggregationmethod"
    ]
  }
</lang-strings>
