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
  <span
    v-if="!scaleValueId.length"
    class="tui-pathwayManual-ratingInput__rateButton"
  >
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
  </span>
  <span v-else class="tui-pathwayManual-ratingInput__editButton">
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
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import RatingPopover from 'pathway_manual/components/RatingPopover';

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
    scaleValueId: {
      type: String,
    },
    comment: {
      type: String,
    },
  },

  data() {
    return {};
  },

  methods: {
    updateRating(ratingData) {
      this.$emit('update-rating', ratingData);
    },

    deleteRating() {
      this.$emit('delete-rating');
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-ratingInput {
  &__heading {
    font-weight: bold;
  }
  &__textarea {
    width: 100%;
    height: 4em;
  }
  &__radioDivider {
    width: 100%;
    margin-bottom: 0;
  }
  &__rateButton {
    float: right;
  }
  &__editButton {
    float: left;
  }
  &__saveButtons {
    float: left;
    width: 100%;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "add_rating",
      "edit_rating",
      "rate"
    ]
  }
</lang-strings>
