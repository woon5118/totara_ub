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
    <template v-slot:trigger>
      <slot name="rating-trigger" />
    </template>
    <span class="tui-pathwayManual-ratingInput__heading">
      {{ $str('select_scale_value', 'pathway_manual') }}
    </span>
    <RadioGroup v-model="inputScaleValueId">
      <Radio
        v-for="(scaleValue, key) in scale.values"
        :key="key"
        :label="scaleValue.name"
        :value="scaleValue.id"
        :name="uniqueRadioName"
      >
        <template>
          {{ scaleValue.name }}
        </template>
      </Radio>
      <hr class="tui-pathwayManual-ratingInput__radioDivider" />
      <Radio :value="noneOptionValue.toString()" :name="uniqueRadioName">
        <template>
          {{ $str('rating_set_to_none', 'pathway_manual') }}
        </template>
      </Radio>
    </RadioGroup>

    <br />
    <span class="tui-pathwayManual-ratingInput__heading">
      {{ $str('add_comment', 'pathway_manual') }}
    </span>
    <textarea
      v-model="inputComment"
      class="tui-pathwayManual-ratingInput__textarea"
    />
    <template v-slot:buttons="{ close }">
      <Button
        :disabled="!inputScaleValueId.length"
        :styleclass="{ small: true, primary: true }"
        :text="$str('rating_done', 'pathway_manual')"
        @click="updateRating(inputScaleValueId, inputComment, close)"
      />
      <ButtonIcon
        v-if="hasInputData"
        :styleclass="{ small: true, alert: true }"
        :aria-label="$str('delete_comment', 'pathway_manual')"
        :text="$str('delete_comment', 'pathway_manual')"
        @click="deleteRating(close)"
      >
        <FlexIcon icon="trash" size="200" />
      </ButtonIcon>
      <Button
        v-else
        :styleclass="{ small: true, primary: false }"
        :text="$str('rating_cancel', 'pathway_manual')"
        @click="cancelRating(close)"
      />
    </template>
  </Popover>
</template>
<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Popover from 'totara_core/components/popover/Popover';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

export const NONE_OPTION_VALUE = -1;

export default {
  components: {
    Button,
    ButtonIcon,
    FlexIcon,
    Popover,
    Radio,
    RadioGroup,
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
      inputScaleValueId: this.scaleValueId.toString(),
      inputComment: this.comment,
      noneOptionValue: NONE_OPTION_VALUE,
    };
  },

  computed: {
    hasInputData() {
      return this.comment.length || this.scaleValueId.length;
    },
    uniqueRadioName() {
      return 'scaleRadio' + this.compId;
    },
  },

  methods: {
    updateRating(inputScaleValueId, inputComment, close) {
      close();
      this.$emit('update-rating', {
        scale_value_id: inputScaleValueId,
        comment: inputComment.trim(),
      });
    },

    deleteRating(close) {
      close();
      this.$emit('delete-rating');
    },

    cancelRating(close) {
      close();
      this.dismissChanges();
    },

    popoverOpenChanged(isOpen) {
      // console.log('openChanged');
      // console.log(this.scaleValueId + '|' + this.inputScaleValueId);
      // console.log(typeof this.scaleValueId + '|' + typeof this.inputScaleValueId);
      if (!isOpen) {
        this.dismissChanges();
      }
    },

    dismissChanges() {
      this.inputComment = this.comment;
      this.inputScaleValueId = this.scaleValueId;
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-ratingInput {
  &__comment-disabled {
    color: var(--tui-color-neutral-4);
    :hover {
      text-decoration: none;
      cursor: default;
    }
  }
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
      "add_comment",
      "comment_done",
      "delete_comment",
      "edit_rating",
      "rate",
      "rating_cancel",
      "rating_done",
      "rating_none",
      "rating_set_to_none",
      "select_scale_value"
    ]
  }
</lang-strings>
