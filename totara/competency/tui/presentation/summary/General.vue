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
    <div class="tui-competencySummaryGeneral__header">
      <div class="tui-competencySummaryGeneral__header_title">
        {{ $str('general', 'moodle') }}
      </div>
      <a
        :href="editUrl"
        class="tui-competencySummaryGeneral__header_edit"
        :title="$str('edit', 'moodle')"
      >
        <FlexIcon icon="edit" size="200" :alt="$str('edit', 'moodle')" />
      </a>
    </div>
    <div class="tui-competencySummaryGeneral__list" role="grid">
      <div
        v-if="competency.display_name"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ $str('fullname', 'totara_competency') }}
        </div>
        <div class="tui-competencySummaryGeneral__list_value" role="gridcell">
          {{ competency.display_name }}
        </div>
      </div>
      <div
        v-if="competency.idnumber"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ $str('idnumber', 'totara_competency') }}
        </div>
        <div class="tui-competencySummaryGeneral__list_value" role="gridcell">
          {{ competency.idnumber }}
        </div>
      </div>
      <div
        v-if="competency.description"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ $str('description', 'totara_competency') }}
        </div>
        <div
          class="tui-competencySummaryGeneral__list_value"
          role="gridcell"
          v-html="competency.description"
        />
      </div>
      <div
        v-if="competency.type"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ $str('type', 'totara_competency') }}
        </div>
        <div class="tui-competencySummaryGeneral__list_value" role="gridcell">
          {{ competency.type.display_name }}
        </div>
      </div>

      <div
        v-for="(field, id) in competency.custom_fields"
        :key="id"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ field.title }}
        </div>
        <div
          class="tui-competencySummaryGeneral__list_value"
          role="gridcell"
          v-html="field.value"
        />
      </div>

      <div
        v-if="hasAssignmentAvailability"
        class="tui-competencySummaryGeneral__list_row"
        role="row"
      >
        <div class="tui-competencySummaryGeneral__list_label" role="rowheader">
          {{ $str('assignmentcreationavailability', 'totara_competency') }}
        </div>
        <div class="tui-competencySummaryGeneral__list_value" role="gridcell">
          <div v-for="val in competency.assign_availability" :key="val">
            {{ assignAvailabilityNames[val] }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const ASSIGNMENT_CREATE_SELF = 1;
const ASSIGNMENT_CREATE_OTHER = 2;

import FlexIcon from 'totara_core/containers/icons/FlexIcon';

import CompetencyQuery from 'totara_competency/graphql/competency';

export default {
  components: { FlexIcon },

  props: {
    competencyId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      competency: {},
    };
  },

  computed: {
    hasAssignmentAvailability() {
      return (
        this.competency.assign_availability != null &&
        this.competency.assign_availability.length > 0
      );
    },

    assignAvailabilityNames() {
      return {
        [ASSIGNMENT_CREATE_SELF]: this.$str(
          'competencyassignavailabilityselfsimple',
          'totara_hierarchy'
        ),
        [ASSIGNMENT_CREATE_OTHER]: this.$str(
          'competencyassignavailabilityothersimple',
          'totara_hierarchy'
        ),
      };
    },

    editUrl() {
      return this.$url('/totara/hierarchy/item/edit.php', {
        prefix: 'competency',
        id: this.competency.id,
      });
    },
  },

  apollo: {
    competency: {
      query: CompetencyQuery,
      variables() {
        return {
          competency_id: this.competencyId,
        };
      },
      update: competency => competency.totara_competency_competency,
    },
  },
};
</script>

<style lang="scss">
.tui-competencySummaryGeneral {
  padding-top: $totara_style-size_4;
  &__header {
    margin-bottom: $totara_style-spacing_2;
    padding-bottom: $totara_style-spacing_1;
    border-bottom: 1px solid $totara_style-color_neutral_5;

    &_title {
      display: inline-block;
      margin-top: auto;
      margin-bottom: auto;
      margin-left: $totara_style-spacing_2;
      font-weight: bold;
      font-size: $totara_style-size_18;
    }

    &_edit {
      float: right;
      margin-bottom: $totara_style-spacing_4;
      padding-left: $totara_style-spacing_2;
    }
  }

  &__list {
    padding: $totara_style-size_8;

    &_label {
      font-weight: bold;
      word-break: break-all;
    }

    &_row {
      display: flex;
      flex-direction: column;
      margin-bottom: $totara_style-size_16;
    }

    &_value {
      word-break: break-all;
    }

    @media (min-width: $totara_style-screen_sm_min) {
      &_label {
        width: 40%;
        padding-right: $totara_style-size_16;
      }

      &_row {
        flex-direction: row;
      }

      &_value {
        width: 60%;
      }
    }
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "name",
      "general",
      "edit"
    ],
    "totara_competency": [
      "fullname",
      "idnumber",
      "description",
      "type",
      "assignmentcreationavailability",
      "achievementpaths",
      "overallratingcalc",
      "anyscalevalue"
    ],
    "totara_hierarchy": [
      "competencyassignavailabilityselfsimple",
      "competencyassignavailabilityothersimple"
    ]
  }
</lang-strings>
