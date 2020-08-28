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
  @package engage_article
-->

<template>
  <Card
    class="tui-articleRelatedCard"
    :clickable="true"
    @click="handleClickCard"
  >
    <img :src="image" :alt="name" class="tui-articleRelatedCard__img" />
    <section class="tui-articleRelatedCard__content">
      <a :href="url">
        {{ name }}
      </a>

      <p>
        <span v-if="timeviewString" class="tui-articleRelatedCard__timeview">
          <TimeIcon
            size="200"
            :alt="$str('time', 'totara_engage')"
            custom-class="tui-articleRelatedCard--dimmed"
          />
          {{ timeviewString }}
        </span>
        <Like
          size="200"
          :alt="$str('like', 'totara_engage')"
          custom-class="tui-articleRelatedCard--dimmed"
        />
        <span>{{ reactions }}</span>
      </p>
    </section>
    <BookmarkButton
      size="300"
      :bookmarked="innerBookmarked"
      :primary="false"
      :circle="false"
      :small="true"
      :transparent="true"
      class="tui-articleRelatedCard__bookmark"
      @click="handleClickBookmark"
    />
  </Card>
</template>

<script>
import Card from 'tui/components/card/Card';
import TimeIcon from 'tui/components/icons/Time';
import Like from 'tui/components/icons/Like';

import { TimeViewType } from 'totara_engage/index';
import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';

export default {
  components: {
    BookmarkButton,
    Card,
    TimeIcon,
    Like,
  },

  props: {
    resourceId: {
      type: [Number, String],
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    bookmarked: {
      type: Boolean,
      default: false,
    },
    image: {
      type: String,
      required: true,
    },
    reactions: {
      type: [Number, String],
      required: true,
    },
    timeview: {
      type: String,
      default: '',
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

  computed: {
    timeviewString() {
      if (TimeViewType.isLessThanFive(this.timeview)) {
        return this.$str('timelessthanfive', 'engage_article');
      }

      if (TimeViewType.isFiveToTen(this.timeview)) {
        return this.$str('timefivetoten', 'engage_article');
      }

      if (TimeViewType.isMoreThanTen(this.timeview)) {
        return this.$str('timemorethanten', 'engage_article');
      }

      return '';
    },
  },

  methods: {
    handleClickBookmark() {
      this.innerBookmarked = !this.innerBookmarked;
      this.$emit('update', this.resourceId, this.innerBookmarked);
    },
    handleClickCard() {
      window.location.href = this.url;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "clock",
      "like"
    ],
    "engage_article": [
      "timelessthanfive",
      "timefivetoten",
      "timemorethanten"
    ]
  }
</lang-strings>
