<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package pathway_manual
-->

<template>
  <div v-if="rating == null" class="tui-bulkManualRatingCell__newRating">
    <RatingPopover
      :comp-id="compId"
      :scale="scale"
      :scale-value-id="scaleValueId"
      :comment="comment"
      @update-rating="updateRating"
      @delete-rating="deleteRating"
    >
      <template v-slot:rating-trigger>
        <Button
          :text="$str('rate', 'pathway_manual')"
          :styleclass="{ small: true }"
          :title="$str('add_rating', 'pathway_manual')"
        />
      </template>
    </RatingPopover>
  </div>
  <div v-else class="tui-bulkManualRatingCell__rating">
    <span class="tui-bulkManualRatingCell__rating">
      <span
        v-if="hasNoneRating"
        class="tui-bulkManualRatingCell__rating-noValue"
      >
        {{ $str('rating_none', 'pathway_manual') }}
      </span>
      <span
        v-else-if="scaleValue"
        class="tui-bulkManualRatingCell__rating-valueName"
      >
        {{ scaleValue.name }}
      </span>
      <span v-if="comment" class="tui-bulkManualRatingCell__rating-hasComment">
        <FlexIcon icon="pathway_manual|comment-filled" size="200" />
      </span>
    </span>
    <span class="tui-bulkManualRatingCell__rating-editRating">
      <RatingPopover
        :comp-id="compId"
        :scale="scale"
        :scale-value-id="scaleValueId"
        :comment="comment"
        @update-rating="updateRating"
        @delete-rating="deleteRating"
      >
        <template v-slot:rating-trigger>
          <ButtonIcon
            :aria-label="$str('edit_rating', 'pathway_manual')"
            :styleclass="{ small: true }"
          >
            <FlexIcon
              icon="edit"
              size="200"
              :title="$str('edit_rating', 'pathway_manual')"
            />
          </ButtonIcon>
        </template>
      </RatingPopover>
    </span>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import RatingPopover from 'pathway_manual/components/RatingPopover';

import { NONE_OPTION_VALUE } from 'pathway_manual/constants';

export default {
  components: {
    Button,
    ButtonIcon,
    FlexIcon,
    RatingPopover,
  },

  props: {
    compId: {
      required: true,
      type: String,
    },
    scale: {
      required: true,
      type: Object,
    },
    rating: {
      type: Object,
    },
  },

  computed: {
    /**
     * Has a rating been made?
     * @returns {boolean}
     */
    hasRating() {
      return this.rating != null;
    },

    /**
     * The comment for this rating.
     * @returns {null|String}
     */
    comment() {
      return this.hasRating ? this.rating.comment : null;
    },

    /**
     * Has a scale value been selected?
     * @returns {boolean}
     */
    hasScaleValue() {
      return this.hasRating && this.rating.scale_value_id != null;
    },

    /**
     * Selected scale value ID.
     * @returns {Number|String}
     */
    scaleValueId() {
      return this.hasRating ? this.rating.scale_value_id : null;
    },

    /**
     * Selected scale value.
     * @returns {null|Object}
     */
    scaleValue() {
      if (this.scaleValueId == null) {
        return null;
      }
      return this.scale.values.find(
        scaleData => scaleData.id === this.scaleValueId
      );
    },

    /**
     * Has a none rating been selected?
     * @returns {boolean}
     */
    hasNoneRating() {
      return (
        this.scaleValueId != null &&
        this.scaleValueId === NONE_OPTION_VALUE.toString()
      );
    },
  },

  methods: {
    /**
     * Notify the parent that this rating has been updated.
     * @param ratingData
     */
    updateRating(ratingData) {
      this.$emit('update-rating', ratingData);
    },

    /**
     * Notify the parent that this rating has been deleted.
     */
    deleteRating() {
      this.$emit('delete-rating');
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "add_rating",
      "edit_rating",
      "rate"
    ]
  }
</lang-strings>
