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
  <Popover
    :triggers="['click']"
    position="bottom-right"
    @open-changed="popoverOpenChanged"
  >
    <h2 class="sr-only">{{ $str('add_rating', 'pathway_manual') }}</h2>
    <div class="tui-bulkManualRatingPopover__scaleValues">
      <label :id="$id('scale-value-heading')">
        {{ $str('select_scale_value', 'pathway_manual') }}
      </label>
      <RadioGroup
        v-model="inputScaleValueId"
        :aria-labelledby="$id('scale-value-heading')"
      >
        <Radio
          v-for="(scaleValue, key) in scale.values"
          :key="key"
          :label="scaleValue.name"
          :value="scaleValue.id"
          :name="$id('scale-radio')"
        >
          <template>
            {{ scaleValue.name }}
          </template>
        </Radio>
        <span class="tui-bulkManualRatingPopover__divider" />
        <Radio :value="noneOptionValue" :name="$id('scale-radio')">
          <template>
            {{ $str('rating_set_to_none', 'pathway_manual') }}
          </template>
        </Radio>
      </RadioGroup>
    </div>
    <div class="tui-bulkManualRatingPopover__comment">
      <label :id="$id('comment-heading')">
        {{ $str('add_comment', 'pathway_manual') }}
      </label>
      <Textarea
        v-model="inputComment"
        class="tui-bulkManualRatingPopover__textarea"
        :aria-labelledby="$id('comment-heading')"
      />
    </div>
    <template v-slot:buttons="{ close }">
      <Button
        :disabled="inputScaleValueId == null"
        :styleclass="{ small: true, primary: true }"
        :text="$str('rating_done', 'pathway_manual')"
        @click="updateRating(close)"
      />
      <ButtonIcon
        v-if="hasInput"
        :styleclass="{ small: true, alert: true }"
        :aria-label="$str('delete')"
        :text="$str('delete')"
        @click="deleteRating(close)"
      >
        <DeleteIcon />
      </ButtonIcon>
      <Button
        v-else
        :styleclass="{ small: true, primary: false }"
        :text="$str('cancel')"
        @click="cancelRating(close)"
      />
    </template>
    <template v-slot:trigger>
      <slot name="rating-trigger" />
    </template>
  </Popover>
</template>
<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/icons/common/Delete';
import Popover from 'totara_core/components/popover/Popover';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Textarea from 'totara_core/components/form/Textarea';

import { NONE_OPTION_VALUE } from 'pathway_manual/constants';

export default {
  components: {
    Button,
    ButtonIcon,
    DeleteIcon,
    Popover,
    Radio,
    RadioGroup,
    Textarea,
  },

  props: {
    scale: {
      required: true,
      type: Object,
    },
    compId: {
      required: true,
      type: String,
    },
    scaleValueId: {
      type: String,
    },
    comment: {
      type: String,
    },
  },

  data() {
    return {
      inputScaleValueId: this.scaleValueId,
      inputComment: this.comment,
    };
  },

  computed: {
    /**
     * Has a scale value been selected or a comment typed?
     * @returns {boolean}
     */
    hasInput() {
      return this.inputScaleValueId != null || this.hasInputComment;
    },

    /**
     * Has a comment been typed in?
     * @returns {boolean}
     */
    hasInputComment() {
      return this.inputComment != null;
    },

    /**
     * The comment made, with unnecessary whitespace removed.
     * @returns {String|null}
     */
    trimmedComment() {
      return this.hasInputComment ? this.inputComment.trim() : null;
    },

    /**
     * Dummy value for selecting a null scale value.
     * @returns {string}
     */
    noneOptionValue() {
      return NONE_OPTION_VALUE.toString();
    },
  },

  methods: {
    /**
     * Close the popover and notify the parent that a rating has been made or updated.
     * @param {function} close
     */
    updateRating(close) {
      close();
      this.$emit('update-rating', {
        scale_value_id: this.inputScaleValueId,
        comment: this.trimmedComment,
      });
    },

    /**
     * Close the popover and notify the parent that the rating has been deleted.
     * @param {function} close
     */
    deleteRating(close) {
      close();
      this.$emit('delete-rating');
    },

    /**
     * Close the popover and clear any draft changes.
     * @param {function} close
     */
    cancelRating(close) {
      close();
      this.dismissChanges();
    },

    /**
     * Clear any changes if the popover is no longer open.
     * @param isOpen
     */
    popoverOpenChanged(isOpen) {
      if (!isOpen) {
        this.dismissChanges();
      }
    },

    /**
     * Clear the selected scale value and comment.
     */
    dismissChanges() {
      this.inputComment = this.comment;
      this.inputScaleValueId = this.scaleValueId;
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "cancel",
      "delete"
    ],
    "pathway_manual": [
      "add_comment",
      "add_rating",
      "comment_done",
      "edit_rating",
      "rate",
      "rating_done",
      "rating_none",
      "rating_set_to_none",
      "select_scale_value"
    ]
  }
</lang-strings>
