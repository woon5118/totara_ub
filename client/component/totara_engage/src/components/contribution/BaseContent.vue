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
      <div class="tui-contributionBaseContent__title">
        <slot name="heading" />
      </div>
      <slot name="bookmark" />
    </section>

    <slot name="filters" />

    <section v-show="!loading || loadingMore">
      <div
        v-if="!showEmptyContribution"
        class="tui-contributionBaseContent__counterContainer"
      >
        <div class="tui-contributionBaseContent__counter">
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
            <template v-if="showEmptyContribution">
              {{ customEmptyContent }}
            </template>
            <template v-else>
              {{ $str('emptycontent', 'totara_engage') }}
            </template>
          </h5>
        </template>
        <CardsGrid
          v-else
          :cards="cards"
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
import PageLoader from 'tui/components/loading/Loader';

export default {
  components: {
    CardsGrid,
    Button,
    PageLoader,
  },

  props: {
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
    showEmptyContribution: Boolean,
    fromLibrary: Boolean,
    customEmptyContent: String,
  },

  data() {
    return {
      initialLoad: true,
    };
  },
  computed: {
    countResource() {
      if (this.totalCards === 1)
        return this.$str(
          this.fromLibrary ? 'itemscountone' : 'resourcecountone',
          'totara_engage',
          this.totalCards
        );
      return this.$str(
        this.fromLibrary ? 'itemscount' : 'resourcecount',
        'totara_engage',
        this.totalCards
      );
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
    "emptycontent",
    "itemscount",
    "itemscountone"
  ],
  "engage_article":[
    "loadmore",
    "viewedresources"
  ]
}
</lang-strings>

<style lang="scss">
.tui-contributionBaseContent {
  &__horizontal {
    padding: var(--gap-8);
  }

  &__vertical {
    .tui-filterBar__filters,
    .tui-contributionBaseContent__cards {
      padding: var(--gap-4);
    }

    .tui-contributionBaseContent__counterContainer {
      padding: 0 var(--gap-2);
    }
    .tui-contributionFilter__sort {
      padding-right: var(--gap-4);
    }
  }

  &__header {
    display: flex;
    justify-content: space-between;
    margin: var(--gap-4) 0 var(--gap-12);

    > :not(:first-child) {
      margin-left: var(--gap-8);
    }

    > :last-child {
      align-self: center;
    }
  }

  &__title {
    @include tui-font-heading-page-title();
    flex-basis: auto;
    flex-grow: 1;
  }

  &__filter {
    display: flex;
    flex-direction: column;
    margin-top: var(--gap-4);
    margin-bottom: var(--gap-4);
  }

  &__cards {
    margin-top: var(--gap-1);
    padding: var(--gap-1);
    & > * + * {
      margin-top: var(--gap-2);
    }
  }

  &__loadMoreContainer {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  &__viewedResources {
    display: flex;
    align-self: center;
    margin-bottom: var(--gap-1);
  }

  &__loadMore {
    display: flex;
    align-self: center;
  }

  &__counterContainer {
    position: relative;
  }

  &__counter {
    @include tui-font-heading-x-small;
    position: absolute;
    top: calc(var(--gap-6) * -2);
    padding: var(--gap-2);
    padding-bottom: 0;
  }

  &__emptyText {
    @include tui-font-body;
    margin-top: var(--gap-2);
  }
}
</style>
