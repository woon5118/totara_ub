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
  @package pathway_manual
-->

<template>
  <div class="tui-pathwayManual__frameworkGroup">
    <div class="tui-pathwayManual__frameworkGroup_titleBar">
      <span
        class="tui-pathwayManual__frameworkGroup_titleBar_icon"
        @click.prevent="toggleOpen"
      >
        <FlexIcon v-if="showContent" icon="nav-expanded" size="200" />
        <FlexIcon v-else icon="nav-expand" size="200" />
      </span>
      <span class="tui-pathwayManual__frameworkGroup_titleBar_title">
        {{ groupTitle }}
      </span>
    </div>
    <Table
      v-show="showContent"
      :data="group.competencies"
      :expandable-rows="false"
      class="tui-pathwayManual__frameworkGroup_table"
    >
      <template v-slot:header-row>
        <HeaderCell size="11">
          {{ columnHeaders[0] }}
        </HeaderCell>
        <HeaderCell size="8">
          <div class="tui-pathwayManual__frameworkGroup_table_block">
            {{ columnHeaders[1] }}
            <LastRatingHelp :is-for-self="isForSelf" />
          </div>
        </HeaderCell>
        <HeaderCell
          size="3"
          class="tui-pathwayManual__frameworkGroup_table_block"
        >
          <div class="tui-pathwayManual__frameworkGroup_table__block">
            {{ columnHeaders[2] }}
            <ScalePopover
              :scale="group"
              :show-descriptions="true"
              position="right"
              class="tui-pathwayManual-frameworkGroup__table_help"
            >
              <FlexIcon icon="info" size="200" />
            </ScalePopover>
          </div>
        </HeaderCell>
        <HeaderCell
          size="3"
          class="tui-pathwayManual__frameworkGroup_table_block"
        />
      </template>
      <template v-slot:row="{ row }">
        <Cell size="11" :column-header="columnHeaders[0]">
          {{ row.competency.display_name }}
        </Cell>
        <Cell size="8" :column-header="columnHeaders[1]">
          <LastRatingBlock
            :latest-rating="row.latest_rating"
            :current-user-id="currentUserId"
          />
        </Cell>
        <Cell size="3" :column-header="columnHeaders[2]">
          <span
            v-if="hasNoneRating(row.competency.id)"
            class="tui-pathwayManual__frameworkGroup_table_noneRating"
          >
            {{ $str('rating_none', 'pathway_manual') }}
          </span>
          <span
            v-else-if="hasRating(row.competency.id)"
            class="tui-pathwayManual__frameworkGroup_table_ratingText"
          >
            {{ getScaleValueName(row.competency.id) }}
          </span>
          <FlexIcon
            v-if="hasComment(row.competency.id)"
            icon="pathway_manual|comment-filled"
            size="200"
          />
        </Cell>
        <Cell size="3" valign="center">
          <RatingInput
            :comp-id="row.competency.id"
            :scale="group"
            :scale-value-id="getScaleValueId(row.competency.id)"
            :comment="getComment(row.competency.id)"
            @update-rating="value => updateRating(row.competency.id, value)"
            @delete-rating="deleteRating(row.competency.id)"
          />
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import LastRatingBlock from 'pathway_manual/components/LastRatingBlock';
import LastRatingHelp from 'pathway_manual/components/LastRatingHelp';
import RatingInput from 'pathway_manual/components/RatingInput';
import ScalePopover from 'totara_competency/components/ScalePopover';
import Table from 'totara_core/components/datatable/Table';

import { NONE_OPTION_VALUE } from 'pathway_manual/components/RatingPopover';

const ROLE_SELF = 'self';

export default {
  components: {
    Cell,
    FlexIcon,
    HeaderCell,
    LastRatingBlock,
    LastRatingHelp,
    RatingInput,
    ScalePopover,
    Table,
  },

  props: {
    currentUserId: {
      required: true,
      type: Number,
    },
    role: {
      required: true,
      type: String,
    },
    group: {
      required: true,
      type: Object,
    },
    expanded: {
      default: true,
      type: Boolean,
    },
    selectedRatings: {
      required: true,
      type: Array,
    },
  },

  data() {
    return {
      showContent: this.expanded,
      showRatingTooltip: false,
      showScaleTooltip: false,
      noneOptionValue: NONE_OPTION_VALUE,
    };
  },

  computed: {
    columnHeaders() {
      return [
        this.$str('competency', 'totara_hierarchy'),
        this.$str('last_rating_given', 'pathway_manual'),
        this.$str('new_rating', 'pathway_manual'),
      ];
    },

    isForSelf() {
      return this.role === ROLE_SELF;
    },

    groupTitle() {
      let count = this.group.competencies.length;

      let string =
        count > 1
          ? 'competency_framework_count_plural'
          : 'competency_framework_count_singular';

      return this.$str(string, 'pathway_manual', {
        name: this.group.framework.display_name,
        count: count,
      });
    },
  },

  methods: {
    toggleOpen() {
      this.showContent = !this.showContent;
    },

    updateRating(compId, ratingData) {
      this.$emit('update-rating', {
        comp_id: compId,
        scale_value_id: ratingData.scale_value_id,
        comment: ratingData.comment,
      });
    },

    deleteRating(compId) {
      this.$emit('delete-rating', compId);
    },

    getComment(compId) {
      let foundRating = this.getRating(compId);
      return foundRating && foundRating.comment ? foundRating.comment : '';
    },

    getScaleValueId(compId) {
      let foundRating = this.getRating(compId);
      return foundRating && foundRating.scale_value_id
        ? foundRating.scale_value_id
        : '';
    },

    getScaleValueName(compId) {
      let currentScaleValueId = this.getScaleValueId(compId);
      if (!currentScaleValueId.length) {
        return '';
      }
      let found = this.group.values.find(
        scaleData => scaleData.id === currentScaleValueId
      );
      return found ? found.name : '';
    },

    getRating(compId) {
      return this.selectedRatings.find(compData => compData.comp_id === compId);
    },

    hasRating(compId) {
      return !!this.getRating(compId);
    },

    hasComment(compId) {
      return !!this.getComment(compId).length;
    },

    hasNoneRating(compId) {
      return this.getScaleValueId(compId) === this.noneOptionValue.toString();
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual__frameworkGroup {
  &:not(:last-child) {
    margin-bottom: var(--tui-gap-7);
  }

  &_titleBar {
    width: 100%;
    padding: var(--tui-gap-2) var(--tui-gap-3);
    background-color: var(--tui-color-neutral-2);

    &_title {
      font-weight: bold;
      font-size: var(--tui-font-size-16);
    }

    &_icon {
      color: var(--tui-color-state);
      .flex-icon {
        vertical-align: text-top;
      }
      cursor: pointer;
    }
  }

  &_table {
    &_block {
      display: block;
    }

    &_help {
      display: inline;
      & .flex-icon {
        cursor: pointer;
      }
    }

    &_flex {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }

    &_noneRating {
      margin-right: var(--tui-gap-1);
      font-style: italic;
    }

    &_ratingText {
      margin-right: var(--tui-gap-1);
      font-weight: bold;
    }
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "competency_framework_count_plural",
      "competency_framework_count_singular",
      "last_rating_given",
      "last_rating_given_other_tooltip",
      "last_rating_given_self_tooltip",
      "new_rating",
      "rater_details_removed",
      "rating_set_to_none"
    ],
    "totara_hierarchy": [
      "competency"
    ]
  }
</lang-strings>
