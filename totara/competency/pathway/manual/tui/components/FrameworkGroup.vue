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
  <Collapsible :label="groupTitle" :initial-state="isExpanded">
    <Table :data="group.competencies" :expandable-rows="false">
      <template v-slot:header-row>
        <HeaderCell size="10">
          {{ $str('competency', 'totara_hierarchy') }}
        </HeaderCell>
        <HeaderCell size="4">
          <div class="tui-bulkManualRatingFrameworkGroup__headerWithHelp">
            {{ $str('last_rating_given', 'pathway_manual') }}
            <LastRatingHelp :is-for-self="role === roleSelf" />
          </div>
        </HeaderCell>
        <HeaderCell size="6">
          <div class="tui-bulkManualRatingFrameworkGroup__headerWithHelp">
            {{ $str('new_rating', 'pathway_manual') }}
            <InfoIconButton
              :aria-label="$str('rating_scale_help', 'totara_competency')"
              position="right"
            >
              <RatingScaleOverview :scale="group" :show-descriptions="true" />
            </InfoIconButton>
          </div>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row }">
        <Cell size="10" :column-header="$str('competency', 'totara_hierarchy')">
          {{ row.competency.display_name }}
        </Cell>
        <Cell
          size="4"
          :column-header="$str('last_rating_given', 'pathway_manual')"
        >
          <LastRatingBlock
            :latest-rating="row.latest_rating"
            :current-user-id="currentUserId"
          />
        </Cell>
        <Cell size="6" :column-header="$str('new_rating', 'pathway_manual')">
          <RatingCell
            :comp-id="row.competency.id"
            :scale="group"
            :rating="getRating(row.competency.id)"
            @update-rating="value => updateRating(row.competency.id, value)"
            @delete-rating="deleteRating(row.competency.id)"
          />
        </Cell>
      </template>
    </Table>
  </Collapsible>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import Collapsible from 'totara_core/components/collapsible/Collapsible';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import LastRatingBlock from 'pathway_manual/components/LastRatingBlock';
import LastRatingHelp from 'pathway_manual/components/LastRatingHelp';
import RatingCell from 'pathway_manual/components/RatingCell';
import RatingScaleOverview from 'totara_competency/components/RatingScaleOverview';
import Table from 'totara_core/components/datatable/Table';
import { ROLE_SELF } from 'pathway_manual/constants';

export default {
  components: {
    Cell,
    Collapsible,
    HeaderCell,
    InfoIconButton,
    LastRatingBlock,
    LastRatingHelp,
    RatingCell,
    RatingScaleOverview,
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
    isExpanded: {
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
      roleSelf: ROLE_SELF,
      showRatingTooltip: false,
      showScaleTooltip: false,
    };
  },

  computed: {
    /**
     * The name of the framework and how many competencies it has.
     * @returns {String}
     */
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
    /**
     * Notify the parent of a new/updated rating.
     * @param {Number} compId
     * @param {Object} ratingData
     */
    updateRating(compId, ratingData) {
      this.$emit('update-rating', {
        comp_id: compId,
        scale_value_id: ratingData.scale_value_id,
        comment: ratingData.comment,
      });
    },

    /**
     * Notify the parent of a deleted rating.
     * @param {Number} compId
     */
    deleteRating(compId) {
      this.$emit('delete-rating', compId);
    },

    /**
     * Get the rating made for a specific competency.
     * @param {Number} compId
     * @returns {Object}
     */
    getRating(compId) {
      return this.selectedRatings.find(compData => compData.comp_id === compId);
    },
  },
};
</script>

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
    "totara_competency": [
      "rating_scale_help"
    ],
    "totara_hierarchy": [
      "competency"
    ]
  }
</lang-strings>
