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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module totara_engage
-->

<template>
  <div
    :class="[
      'tui-contributionBaseContent',
      `tui-contributionBaseContent__${gridDirection}`,
    ]"
  >
    <section v-if="showHeading" class="tui-contributionBaseContent__navButtons">
      <slot name="buttons" />
    </section>

    <section v-if="showHeading" class="tui-contributionBaseContent__header">
      <div class="tui-contributionBaseContent__header__title">
        <slot name="heading" />
      </div>
      <slot name="bookmark" />
    </section>

    <slot name="filters" />

    <section v-show="!loading || loadingMore">
      <div class="tui-contributionBaseContent__counterContainer">
        <div class="tui-contributionBaseContent__counterContainer__counter">
          <template v-if="customTitle">
            {{ customTitle }}
          </template>
          <template v-else>
            {{ countResource }}
          </template>
        </div>
      </div>
    </section>

    <section
      v-show="!loading || loadingMore"
      class="tui-contributionBaseContent__cards"
    >
      <slot name="cards">
        <template v-if="showEmptyContent && cards.length === 0">
          <h5 class="tui-contributionBaseContent__emptyText">
            {{ $str('emptycontent', 'totara_engage') }}
          </h5>
        </template>
        <CardsGrid
          v-else
          :cards="cards"
          :units="units"
          :is-loading="loading"
          :show-footnotes="showFootnotes"
          @scrolledtobottom="scrolledToBottom"
        />
        <div
          v-if="isLoadMoreVisible && cards.length < totalCards && !loading"
          class="tui-contributionBaseContent__loadMoreContainer"
        >
          <div class="tui-contributionBaseContent__viewedResources">
            <template v-if="customLoadMoreText">
              {{ customLoadMoreText }}
            </template>
            <template v-else>
              {{ $str('viewedresources', 'engage_article', cards.length) }}
              {{ $str('resourcecount', 'totara_engage', totalCards) }}
            </template>
          </div>
          <Button
            class="tui-contributionBaseContent__loadMore"
            :text="$str('loadmore', 'engage_article')"
            @click="loadMore"
          />
        </div>
      </slot>
    </section>

    <PageLoader :fullpage="false" :loading="loading && !loadingMore" />
  </div>
</template>

<script>
import CardsGrid from 'totara_engage/components/contribution/CardsGrid';
import Button from 'tui/components/buttons/Button';
import PageLoader from 'tui/components/loader/Loader';

export default {
  components: {
    CardsGrid,
    Button,
    PageLoader,
  },

  props: {
    units: {
      type: [Number, String],
      required: true,
    },
    loading: {
      type: Boolean,
      required: true,
    },
    loadingMore: {
      type: Boolean,
      required: true,
    },
    isLoadMoreVisible: {
      type: Boolean,
      default: false,
    },
    cards: {
      type: Array,
      default: () => [],
    },
    totalCards: Number,
    showHeading: {
      type: Boolean,
      default: true,
    },
    gridDirection: {
      type: String,
      required: true,
    },
    showFootnotes: {
      type: Boolean,
      default: false,
    },
    customTitle: String,
    customLoadMoreText: String,
    showEmptyContent: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      initialLoad: true,
    };
  },
  computed: {
    countResource() {
      if (this.totalCards === 1)
        return this.$str('resourcecountone', 'totara_engage', this.totalCards);
      return this.$str('resourcecount', 'totara_engage', this.totalCards);
    },
  },
  watch: {
    cards() {
      this.initialLoad = false;
    },
  },
  methods: {
    scrolledToBottom() {
      this.$emit('scrolled-to-bottom');
    },

    loadMore() {
      this.$emit('load-more');
    },
  },
};
</script>

<lang-strings>
{
  "totara_engage": [
    "resourcecount",
    "resourcecountone",
    "emptycontent"
  ],
  "engage_article":[
    "loadmore",
    "viewedresources"
  ]
}
</lang-strings>
