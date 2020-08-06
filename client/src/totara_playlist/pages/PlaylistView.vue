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
  <Layout class="tui-playlistView">
    <template v-slot:column="{ units, boundaryName }">
      <ContributionBaseContent
        :units="units"
        :loading="!!$apollo.loading"
        :loading-more="loadingMore"
        :total-cards="contribution.cursor.total"
        :show-heading="!$apollo.loading"
        grid-direction="horizontal"
      >
        <template v-slot:buttons>
          <ResourceNavigationBar :back-button="backButton" />
        </template>
        <template v-slot:heading>
          <HeaderBox
            :playlist-id="playlistId"
            :title="playlist.name"
            :update-able="canUpdate"
            @update-playlist="updatePlaylist"
          />
        </template>
        <template v-slot:bookmark>
          <BookmarkButton
            v-if="!playlist.owned"
            :primary="false"
            :circle="true"
            :bookmarked="bookmarked"
            size="300"
            class="tui-playlistView__bookmark"
            @click="updateBookmark"
          />
        </template>
        <template v-if="contribution.cards && playlist.access" v-slot:cards>
          <PlaylistResourcesGrid
            :max-units="units"
            :playlist-id="playlistId"
            :size="boundaryName"
            :cards="contribution.cards"
            :contributable="playlist.contributable"
            :access="playlist.access"
            :update-able="canUpdate"
            :is-loading="$apollo.loading"
            @refetch="refetchCards"
            @resource-added="refetchCards"
            @resource-reordered="resourceReordered"
          />

          <div
            v-if="
              isLoadMoreVisible &&
                contribution.cards.length < contribution.cursor.total &&
                !$apollo.loading
            "
            class="tui-playlistView__loadMoreContainer"
          >
            <div class="tui-playlistView__viewedResources">
              {{
                $str(
                  'viewedresources',
                  'engage_article',
                  contribution.cards.length
                )
              }}
              {{
                $str(
                  'resourcecount',
                  'totara_engage',
                  contribution.cursor.total
                )
              }}
            </div>
            <Button
              class="tui-playlistView__loadMore"
              :text="$str('loadmore', 'engage_article')"
              @click="loadMore"
            />
          </div>
        </template>
      </ContributionBaseContent>
    </template>

    <template v-slot:sidepanel>
      <PlaylistSidePanel
        :playlist-id="playlistId"
        @playlist-updated="refetchCards"
      />
    </template>
  </Layout>
</template>

<script>
import PlaylistResourcesGrid from 'totara_playlist/components/grid/PlaylistResourcesGrid';
import PlaylistSidePanel from 'totara_playlist/components/sidepanel/PlaylistSidePanel';
import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';
import ContributionBaseContent from 'totara_engage/components/contribution/BaseContent';
import HeaderBox from 'totara_playlist/components/page/HeaderBox';
import Layout from 'tui/components/layouts/LayoutOneColumnWithSidePanel';
import ResourceNavigationBar from 'totara_engage/components/header/ResourceNavigationBar';
import apolloClient from 'tui/apollo_client';

// GraphQL
import getPlaylist from 'totara_playlist/graphql/get_playlist';
import getCards from 'totara_playlist/graphql/cards';
import updateBookmark from 'totara_engage/graphql/update_bookmark';
import updateCardOrder from 'totara_playlist/graphql/update_card_order';

export default {
  components: {
    PlaylistResourcesGrid,
    PlaylistSidePanel,
    BookmarkButton,
    ContributionBaseContent,
    HeaderBox,
    Layout,
    ResourceNavigationBar,
  },

  props: {
    playlistId: {
      required: true,
      type: Number,
    },

    backButton: {
      type: Object,
      required: false,
    },
  },

  apollo: {
    playlist: {
      query: getPlaylist,
      variables() {
        return {
          id: this.playlistId,
        };
      },
      result({ data: { playlist } }) {
        this.bookmarked = playlist.bookmarked;
      },
    },
    contribution: {
      query: getCards,
      variables() {
        return {
          id: this.playlistId,
          source: 'pl.' + this.playlistId,
        };
      },
    },
  },

  data() {
    return {
      playlist: {},
      bookmarked: false,
      contribution: {
        cursor: {
          total: 0,
          next: null,
        },
        cards: [],
      },
      isLoadMoreVisible: false,
      loadingMore: false,
    };
  },

  computed: {
    canUpdate() {
      return this.playlist.owned || this.playlist.manageable;
    },
  },

  methods: {
    updateBookmark() {
      this.bookmarked = !this.bookmarked;
      this.$apollo.mutate({
        mutation: updateBookmark,
        refetchAll: false,
        variables: {
          itemid: this.playlistId,
          component: 'totara_playlist',
          bookmarked: this.bookmarked,
        },
      });
    },

    refetchCards() {
      this.$apollo.queries.contribution.refetch();
    },

    resourceReordered(obj) {
      const { list, instanceid, destinationIndex, playlistId } = obj;
      this.contribution = Object.assign({}, this.contribution, { cards: list });

      this.$apollo.mutate({
        mutation: updateCardOrder,
        refetchQueries: [
          {
            query: getCards,
            variables: {
              id: playlistId,
            },
          },
        ],
        variables: {
          id: playlistId,
          instanceid,
          order: destinationIndex,
        },
      });
    },

    async scrolledToBottom() {
      if (this.isLoadMoreVisible) {
        return;
      }
      await this.loadMoreItems();
      this.isLoadMoreVisible = true;
    },

    async loadMore() {
      await this.loadMoreItems();
      this.isLoadMoreVisible = false;
    },

    /**
     * Load additional items and append to list
     *
     */
    async loadMoreItems() {
      if (!this.contribution.cursor.next) {
        return;
      }
      this.loadingMore = true;
      this.$apollo.queries.contribution.fetchMore({
        variables: {
          id: this.pageProps.playlistId,
          cursor: this.contribution.cursor.next,
        },

        updateQuery: (previousResult, { fetchMoreResult }) => {
          const oldData = previousResult.contribution;
          const newData = fetchMoreResult.contribution;
          const newList = oldData.cards.concat(newData.cards);
          this.loadingMore = false;

          return {
            contribution: {
              cursor: newData.cursor,
              cards: newList,
            },
          };
        },
      });
    },

    /**
     *
     * @param {Object} playlist
     */
    updatePlaylist(playlist) {
      apolloClient.writeQuery({
        query: getPlaylist,
        variables: {
          id: this.playlistId,
        },

        data: {
          playlist,
        },
      });
    },
  },
};
</script>

<lang-strings>
{
  "totara_engage": [
    "resourcecount"
  ],
  "engage_article":[
    "loadmore",
    "viewedresources"
  ]
}
</lang-strings>
