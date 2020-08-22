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
  @module totara_playlist
-->

<template>
  <BaseCard
    :data-card-unique="instanceId"
    :href="url"
    :show-footnotes="showFootnotes"
    :footnotes="footnotes"
    class="tui-totaraPlaylist-playlistCard"
    @remove-resource="$emit('remove-resource', $event)"
    @mouseover="$_handleHovered(true)"
    @mouseleave="$_handleHovered(false)"
  >
    <ImageHeader slot="header-image" :show-cover="hovered">
      <div slot="image" class="tui-totaraPlaylist-playlistCard__imageHeader">
        <div
          class="tui-totaraPlaylist-playlistCard__numberOfResourcesContainer"
        >
          <div class="tui-totaraPlaylist-playlistCard__numberOfResources">
            <!-- This box will be gone if the card is hovered -->
            <p>{{ extraData.resources }}</p>
          </div>
        </div>

        <div class="tui-totaraPlaylist-playlistCard__imageContainer">
          <img
            :alt="name"
            :src="extraData.image"
            class="tui-totaraPlaylist-playlistCard__image"
          />
        </div>
      </div>

      <div slot="actions" class="tui-totaraPlaylist-playlistCard__icons">
        <ButtonIcon
          :aria-label="$str('share', 'totara_engage')"
          :styleclass="{ primary: false, circle: true }"
          @click.prevent="$_handleShare"
        >
          <ShareIcon />
        </ButtonIcon>

        <ButtonIcon
          v-if="extraData.actions"
          :aria-label="$str('more', 'totara_engage')"
          :styleclass="{ primary: false, circle: true }"
          @click.prevent="$_handleMoreActions"
        >
          <MoreIcon />
        </ButtonIcon>
      </div>
    </ImageHeader>

    <CardHeader slot="header" class="tui-totaraPlaylist-playlistCard__header">
      <BookmarkButton
        v-show="!owned"
        slot="first"
        size="300"
        :bookmarked="innerBookmarked"
        :primary="false"
        :circle="false"
        :small="true"
        :transparent="true"
        class="tui-totaraPlaylist-playlistCard__bookmark"
        @click="updateBookmark"
      />
      <h4
        :id="labelId"
        slot="second"
        class="tui-totaraPlaylist-playlistCard__title"
      >
        {{ name }}
      </h4>
    </CardHeader>

    <StarRating
      slot="info-content"
      :rating="showRating"
      :read-only="true"
      :increment="0.1"
      :max-rating="5"
      :title="starTitle"
      class="tui-totaraPlaylist-playlistCard__rating"
    />

    <div slot="footer" class="tui-totaraPlaylist-playlistCard__footer">
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
        custom-class="tui-totaraPlaylist-playlistCard__visibilityIcon"
      />
    </div>
  </BaseCard>
</template>

<script>
import BaseCard from 'totara_engage/components/card/BaseCard';
import ImageHeader from 'totara_engage/components/card/ImageHeader';
import CardHeader from 'totara_engage/components/card/CardHeader';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import StatIcon from 'totara_engage/components/icons/StatIcon';
import StarRating from 'totara_engage/components/icons/StarRating';
import ShareIcon from 'totara_engage/components/icons/Share';
import MoreIcon from 'totara_engage/components/icons/More';
import { cardMixin, AccessManager } from 'totara_engage/index';
import AccessIcon from 'totara_engage/components/icons/access/computed/AccessIcon';
import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';

// GraphQL
import updateBookmark from 'totara_engage/graphql/update_bookmark';

export default {
  components: {
    ButtonIcon,
    BaseCard,
    ImageHeader,
    CardHeader,
    StatIcon,
    StarRating,
    ShareIcon,
    MoreIcon,
    AccessIcon,
    BookmarkButton,
  },

  mixins: [cardMixin],

  data() {
    return {
      // Assign to the inner property, so that we don't have to mutate the parent.
      innerBookmarked: this.bookmarked,
      hovered: false,
      extraData: JSON.parse(this.extra),
      statIcons: [
        {
          type: 'comment',
          icon: 'totara_engage|comment',
          title: this.$str(
            'numberofcomments',
            'totara_engage',
            this.totalComments
          ),
          statNumber: this.totalComments,
        },
      ],
    };
  },

  computed: {
    showRating() {
      return this.rating;
    },
    starTitle() {
      if (this.extraData.ratingCount <= 1) {
        return this.$str(
          'numberofpersonrating',
          'totara_engage',
          this.extraData.ratingCount
        );
      }

      return this.$str(
        'numberofpeoplerating',
        'totara_engage',
        this.extraData.ratingCount
      );
    },
  },

  created() {
    if (AccessManager.isPublic(this.access)) {
      // Adding stat icon depending the visibility
      this.statIcons.push({
        type: 'share',
        icon: 'totara_engage|share',
        title: this.$str('numberofshares', 'totara_engage', this.sharedbycount),
        statNumber: this.sharedbycount,
      });
    }
  },

  methods: {
    /**
     * Changing the state of image hovering.
     * @param {boolean} value
     */
    $_handleHovered(value) {
      this.hovered = value;
    },

    $_handleShare() {
      // Todo: update this functionality
    },

    $_handleMoreActions() {
      // Todo: update this functionality
    },

    updateBookmark() {
      this.innerBookmarked = !this.innerBookmarked;
      this.$apollo.mutate({
        mutation: updateBookmark,
        refetchAll: false,
        refetchQueries: [
          'totara_playlist_playlist_links',
          'totara_engage_contribution_cards',
        ],
        variables: {
          itemid: this.instanceId,
          component: 'totara_playlist',
          bookmarked: this.innerBookmarked,
        },
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "numberofcomments",
      "numberofshares",
      "share",
      "more",
      "numberofpeoplerating",
      "numberofpersonrating"
    ]
  }
</lang-strings>
