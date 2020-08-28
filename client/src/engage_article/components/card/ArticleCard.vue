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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module engage_article
-->

<template>
  <BaseCard
    :data-card-unique="instanceId"
    :href="url"
    class="tui-engageArticle-articleCard"
    :show-footnotes="showFootnotes"
    :footnotes="footnotes"
    @mouseover="$_handleHovered(true)"
    @mouseleave="$_handleHovered(false)"
  >
    <ImageHeader
      slot="header-image"
      :show-cover="hovered"
      class="tui-engageArticle-articleCard__imageheader"
    >
      <img
        slot="image"
        :alt="name"
        :src="extraData.image"
        class="tui-engageArticle-articleCard__image"
      />

      <div slot="actions" class="tui-engageArticle-articleCard__icons">
        <ButtonIcon
          v-for="(action, i) in actions"
          :key="i"
          :aria-label="action.alt"
          :styleclass="{ primary: false, circle: true }"
        >
          <component :is="action.component" />
        </ButtonIcon>
      </div>
    </ImageHeader>

    <CardHeader slot="header" class="tui-engageArticle-articleCard__header">
      <BookmarkButton
        v-show="!owned"
        slot="first"
        size="300"
        :bookmarked="innerBookmarked"
        :primary="false"
        :circle="false"
        :small="true"
        :transparent="true"
        class="tui-engageArticle-articleCard__bookmark"
        @click="updateBookmark"
      />

      <h3
        :id="labelId"
        slot="second"
        class="tui-engageArticle-articleCard__title"
      >
        {{ name }}
      </h3>

      <div
        v-if="extraData.timeview"
        slot="third"
        class="tui-engageArticle-articleCard__subTitle"
      >
        <TimeIcon
          size="200"
          :alt="$str('time', 'totara_engage')"
          custom-class="tui-icon--dimmed"
        />
        <span>{{ getTimeView }}</span>
      </div>
    </CardHeader>

    <div slot="footer" class="tui-engageArticle-articleCard__footer">
      <StatIcon
        v-for="statIcon in statIcons"
        :key="statIcon.type"
        :icon="statIcon.icon"
        :title="statIcon.title"
        :stat-number="statIcon.statNumber"
      />

      <AccessIcon
        :access="access"
        size="300"
        custom-class="tui-engageArticle-articleCard__visibilityIcon"
      />
    </div>
  </BaseCard>
</template>

<script>
import BaseCard from 'totara_engage/components/card/BaseCard';
import ImageHeader from 'totara_engage/components/card/ImageHeader';
import StatIcon from 'totara_engage/components/icons/StatIcon';
import CardHeader from 'totara_engage/components/card/CardHeader';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import ShareIcon from 'tui/components/icons/Share';
import AddToListIcon from 'tui/components/icons/AddToList';
import MoreIcon from 'tui/components/icons/More';
import { cardMixin, AccessManager, TimeViewType } from 'totara_engage/index';
import AccessIcon from 'totara_engage/components/icons/access/computed/AccessIcon';
import TimeIcon from 'tui/components/icons/Time';
import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';

// GraphQL
import updateBookmark from 'totara_engage/graphql/update_bookmark';

export default {
  components: {
    ButtonIcon,
    BaseCard,
    ImageHeader,
    StatIcon,
    CardHeader,
    ShareIcon,
    AddToListIcon,
    MoreIcon,
    AccessIcon,
    TimeIcon,
    BookmarkButton,
  },

  mixins: [cardMixin],

  data() {
    return {
      // Assign the value to the inner child, as we do not want to mutate the prop.
      innerBookmarked: this.bookmarked,
      hovered: false,
      statIcons: [
        {
          type: 'reaction',
          title: this.$str(
            'numberoflikes',
            'totara_engage',
            this.totalReactions
          ),
          icon: 'totara_core|like',
          statNumber: this.totalReactions,
        },
        {
          type: 'comment',
          title: this.$str(
            'numberofcomments',
            'totara_engage',
            this.totalComments
          ),
          icon: 'totara_engage|comment',
          statNumber: this.totalComments,
        },
      ],
      actions: [
        {
          alt: this.$str('addtoplaylist', 'engage_article'),
          component: 'AddToListIcon',
        },
        {
          alt: this.$str('share', 'totara_engage'),
          component: 'ShareIcon',
        },
        {
          alt: this.$str('more', 'totara_engage'),
          component: 'MoreIcon',
        },
      ],
      extraData: JSON.parse(this.extra),
    };
  },

  computed: {
    getTimeView() {
      if (TimeViewType.isLessThanFive(this.extraData.timeview)) {
        return this.$str('timelessthanfive', 'engage_article');
      } else if (TimeViewType.isFiveToTen(this.extraData.timeview)) {
        return this.$str('timefivetoten', 'engage_article');
      } else if (TimeViewType.isMoreThanTen(this.extraData.timeview)) {
        return this.$str('timemorethanten', 'engage_article');
      }
      return null;
    },
  },

  created() {
    // Add more stat icons depending on the visibility status of the card
    if (AccessManager.isPublic(this.access)) {
      this.statIcons = this.statIcons.concat([
        {
          type: 'share',
          title: this.$str(
            'numberofshares',
            'totara_engage',
            this.sharedbycount
          ),
          icon: 'totara_engage|share',
          statNumber: this.sharedbycount,
        },
        {
          type: 'playlistUsage',
          title: this.$str(
            'numberwithinplaylist',
            'engage_article',
            this.extraData.usage
          ),
          icon: 'totara_engage|add-to-playlist',
          statNumber: this.extraData.usage,
        },
      ]);
    }
  },

  methods: {
    $_handleHovered(hovered) {
      this.hovered = hovered;
    },

    updateBookmark() {
      this.innerBookmarked = !this.innerBookmarked;
      this.$apollo.mutate({
        mutation: updateBookmark,
        refetchAll: false,
        refetchQueries: ['totara_engage_contribution_cards'],
        variables: {
          itemid: this.instanceId,
          component: 'engage_article',
          bookmarked: this.innerBookmarked,
        },
      });
    },
  },
};
</script>

<lang-strings>
  {
    "engage_article": [
      "addtoplaylist",
      "numberwithinplaylist",
      "timelessthanfive",
      "timefivetoten",
      "timemorethanten"
    ],
    "totara_engage": [
      "more",
      "share",
      "numberofcomments",
      "numberoflikes",
      "numberofshares",
      "clock"
    ]
  }
</lang-strings>
