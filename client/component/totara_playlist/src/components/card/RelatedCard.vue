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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_playlist
-->

<template>
  <Card
    class="tui-playlistRelatedCard"
    :clickable="true"
    @click="handleClickCard"
  >
    <a :href="url" />
    <div
      class="tui-playlistRelatedCard__header"
      :style="{
        'background-image': `url('${image}')`,
      }"
    >
      <span>{{ resources }}</span>
    </div>

    <section class="tui-playlistRelatedCard__content">
      <span>{{ name }}</span>
      <span>{{ fullname }}</span>
      <StarRating
        :rating="rating"
        :read-only="true"
        :increment="0.1"
        :max-rating="5"
      />
    </section>
    <BookmarkButton
      size="300"
      :bookmarked="innerBookmarked"
      :primary="false"
      :circle="false"
      :small="true"
      :transparent="true"
      class="tui-playlistRelatedCard__bookmark"
      @click="handleClickBookmark"
    />
  </Card>
</template>

<script>
import Card from 'tui/components/card/Card';

import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';
import StarRating from 'totara_engage/components/icons/StarRating';

export default {
  components: {
    BookmarkButton,
    Card,
    StarRating,
  },

  props: {
    bookmarked: {
      type: Boolean,
      default: false,
    },
    fullname: {
      type: String,
      required: true,
    },
    image: {
      type: String,
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    playlistId: {
      type: [Number, String],
      required: true,
    },
    rating: {
      type: Number,
      required: true,
    },
    resources: {
      type: Number,
      required: true,
    },
    url: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      innerBookmarked: this.bookmarked,
    };
  },

  methods: {
    handleClickBookmark() {
      this.innerBookmarked = !this.innerBookmarked;
      this.$emit('update', this.playlistId, this.innerBookmarked);
    },
    handleClickCard() {
      window.location.href = this.url;
    },
  },
};
</script>

<style lang="scss">
.tui-playlistRelatedCard {
  display: flex;
  min-width: 120px;
  height: 82px;
  background-color: var(--color-neutral-1);

  &__header {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    margin-right: var(--gap-2);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    border-top-left-radius: var(--border-radius-normal);
    border-bottom-left-radius: var(--border-radius-normal);

    > :first-child {
      @include tui-font-heading-label-small();
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background-color: var(--color-neutral-1);
      border-radius: 50%;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: var(--gap-4) 0 var(--gap-2) 0;
    overflow: hidden;

    & > * {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    > :first-child {
      @include tui-font-heading-label-small();
      color: inherit;
      text-decoration: none;
    }

    > :nth-child(2) {
      @include tui-font-body-x-small();
    }

    > :last-child {
      margin-top: auto;
      margin-bottom: 0;

      .tui-totaraEngage-star {
        width: var(--font-size-14);
        height: var(--font-size-14);

        &__filled {
          stop-color: var(--color-chart-background-2);
        }

        &__unfilled {
          stop-color: var(--color-neutral-1);
        }
      }
    }
  }

  &__bookmark {
    align-self: flex-start;
    // neutralize the default icon padding
    margin-top: -2px;
  }
}
</style>
