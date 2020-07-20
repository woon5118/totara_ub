<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module pathway_manual
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
import Button from 'tui/components/buttons/Button';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import FlexIcon from 'tui/components/icons/FlexIcon';
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
