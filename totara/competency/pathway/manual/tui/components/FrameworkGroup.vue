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
          <strong>{{ $str('competency', 'totara_hierarchy') }}</strong>
        </HeaderCell>
        <HeaderCell size="8">
          <div class="tui-pathwayManual__frameworkGroup_table_block">
            <strong>{{ $str('last_rating_given', 'pathway_manual') }}</strong>
            <div
              class="tui-pathwayManual__frameworkGroup_table_help"
              @mouseover="showRatingTooltip = true"
              @mouseleave="showRatingTooltip = false"
            >
              <FlexIcon icon="info" size="200" />
              <Tooltip :display="showRatingTooltip">
                <span v-if="isForSelf">
                  {{ $str('last_rating_given_self_tooltip', 'pathway_manual') }}
                </span>
                <span v-else>
                  {{
                    $str('last_rating_given_other_tooltip', 'pathway_manual')
                  }}
                </span>
              </Tooltip>
            </div>
          </div>
        </HeaderCell>
        <HeaderCell
          size="3"
          class="tui-pathwayManual__frameworkGroup_table_block"
        >
          <div class="tui-pathwayManual__frameworkGroup_table_block">
            <strong>{{ $str('new_rating', 'pathway_manual') }}</strong>
            <div
              class="tui-pathwayManual__frameworkGroup_table_help"
              @mouseover="showScaleTooltip = true"
              @mouseleave="showScaleTooltip = false"
            >
              <FlexIcon icon="info" size="200" />
              <ScaleTooltip
                :scale="group"
                :display="showScaleTooltip"
                :show-descriptions="true"
              />
            </div>
          </div>
        </HeaderCell>
        <HeaderCell
          size="3"
          class="tui-pathwayManual__frameworkGroup_table_block"
        />
      </template>
      <template v-slot:row="{ row }">
        <Cell size="11">
          {{ row.competency.display_name }}
        </Cell>
        <Cell size="8">
          <span v-if="row.last_rating">
            <span v-if="row.last_rating.scale_value">
              {{ row.last_rating.scale_value.name }}
            </span>
            <span v-else>
              {{ $str('rating_set_to_none', 'pathway_manual') }}
            </span>
            <br />
            {{ row.last_rating.date }}<br />
            {{ getRater(row.last_rating.rater) }}
          </span>
        </Cell>
        <Cell size="3">
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
import ScaleTooltip from 'totara_competency/components/ScaleTooltip';
import RatingInput from 'pathway_manual/components/RatingInput';
import Table from 'totara_core/components/datatable/Table';
import Tooltip from 'totara_competency/components/Tooltip';

import { NONE_OPTION_VALUE } from 'pathway_manual/components/RatingPopover';

const ROLE_SELF = 'self';

export default {
  components: {
    Cell,
    FlexIcon,
    HeaderCell,
    ScaleTooltip,
    RatingInput,
    Table,
    Tooltip,
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

    getRater(rater) {
      if (rater) {
        // Only display the rater's name if it's not the current user.
        return parseInt(rater.id) === this.currentUserId
          ? ''
          : '(' + rater.fullname + ')';
      } else {
        return this.$str('rater_details_removed', 'pathway_manual');
      }
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
