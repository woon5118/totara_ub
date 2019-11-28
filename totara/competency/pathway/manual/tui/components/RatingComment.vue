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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package pathway_manual
-->

<template>
  <span v-if="hasRating">
    <Popover
      :triggers="['click']"
      position="bottom-right"
      @open-changed="dismissChanges"
    >
      <template v-slot:trigger>
        <ButtonIcon
          :styleclass="{ transparent: true }"
          :aria-label="$str('comment', 'pathway_manual')"
        >
          <FlexIcon
            :icon="commentIcon"
            size="300"
            :title="$str('comment', 'pathway_manual')"
          />
        </ButtonIcon>
      </template>
      {{ $str('comment', 'pathway_manual') }}
      <textarea
        v-model="inputComment"
        class="tui-pathwayManual-ratingComment__textarea"
      />
      <template v-slot:buttons="{ close }">
        <ButtonIcon
          v-if="attachedComment.length"
          :styleclass="{ transparent: true, small: true }"
          :aria-label="$str('delete_comment', 'pathway_manual')"
          @click="deleteComment(close)"
        >
          <FlexIcon
            icon="trash"
            size="200"
            :title="$str('delete_comment', 'pathway_manual')"
          />
          {{ $str('delete_comment', 'pathway_manual') }}
        </ButtonIcon>
        <Button
          :styleclass="{ small: true, primary: true }"
          :text="$str('comment_done', 'pathway_manual')"
          @click="updateComment(inputComment, close)"
        />
      </template>
    </Popover>
  </span>
  <span v-else>
    <ButtonIcon
      :styleclass="{ transparent: true }"
      :aria-label="$str('comment', 'pathway_manual')"
      class="tui-pathwayManual-ratingComment__comment-disabled"
    >
      <FlexIcon
        icon="pathway_manual|comment"
        size="300"
        custom-class="tui-pathwayManual-ratingComment__comment-disabled"
        :title="$str('comment', 'pathway_manual')"
      />
    </ButtonIcon>
  </span>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Popover from 'totara_core/components/popover/Popover';

export default {
  components: {
    Button,
    ButtonIcon,
    FlexIcon,
    Popover,
  },

  props: {
    hasRating: {
      required: true,
      type: Boolean,
    },
    attachedComment: {
      type: String,
    },
  },

  data() {
    return {
      inputComment: this.attachedComment,
    };
  },

  computed: {
    commentIcon() {
      let iconName = 'pathway_manual|comment';
      if (this.hasRating && this.attachedComment.length) {
        iconName += '-filled';
      }
      return iconName;
    },
  },

  watch: {
    attachedComment: function(value) {
      this.inputComment = value;
    },
  },

  methods: {
    updateComment(inputComment, close) {
      close();
      this.$emit('update-comment', inputComment.trim());
    },

    deleteComment(close) {
      close();
      this.$emit('update-comment', '');
    },

    dismissChanges(popoverVisible) {
      if (!popoverVisible) {
        // When closing popover, dismiss unsaved content.
        this.inputComment = this.attachedComment;
      }
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-ratingComment {
  &__comment-disabled {
    color: var(--tui-color-neutral-4);
    :hover {
      text-decoration: none;
      cursor: default;
    }
  }
  &__textarea {
    width: 100%;
    height: 4em;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "comment",
      "comment_done",
      "delete_comment"
    ]
  }
</lang-strings>
