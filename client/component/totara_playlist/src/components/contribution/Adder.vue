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
  @module totara_playlist
-->

<template>
  <EngageAdderModal
    :title="$str('selectcontent', 'totara_playlist')"
    :open="showAdder"
    :cards="contribution.cards"
    :filter-value="filterValue"
    filter-component="totara_playlist"
    filter-area="adder"
    @added="adderUpdate"
    @cancel="$emit('close', $event)"
    @topic="filterTopic"
    @search="filterSearch"
    @section="filterSection"
  />
</template>

<script>
import EngageAdderModal from 'totara_engage/components/modal/EngageAdderModal';

// GraphQL
import resources from 'totara_playlist/graphql/resources';
import addResources from 'totara_playlist/graphql/add_resources';

// Mixins
import ContributionMixin from 'totara_engage/mixins/contribution_mixin';

export default {
  components: {
    EngageAdderModal,
  },

  mixins: [ContributionMixin],

  props: {
    playlistId: {
      type: [String, Number],
      required: true,
    },
    showAdder: Boolean,
  },

  watch: {
    showAdder(value) {
      this.skipCardsQuery = !value;
    },
  },

  created() {
    // Overwrite values defined in ContributionMixin.
    this.skipCardsQuery = true;
  },

  apollo: {
    contribution: {
      query: resources,
      fetchPolicy: 'network-only',
      variables() {
        return Object.assign({}, this.filterValue, {
          playlist_id: this.playlistId,
          area: 'adder',
          include_footnotes: false,
        });
      },
      update({ resources: { cursor, cards } }) {
        return {
          cursor: cursor,
          cards: this.canContribute ? this.$_addContributeCard(cards) : cards,
        };
      },
      skip() {
        return this.skipCardsQuery;
      },
    },
  },

  methods: {
    adderUpdate(selection) {
      this.$apollo
        .mutate({
          mutation: addResources,
          refetchAll: false,
          refetchQueries: ['totara_playlist_cards'],
          variables: {
            playlistid: this.playlistId,
            resources: selection.map(item => {
              const resource = JSON.parse(item);
              return resource.itemid;
            }),
          },
        })
        .finally(() => {
          this.$emit('close');
        });
    },
  },
};
</script>

<lang-strings>
{
  "totara_playlist": [
    "selectcontent"
  ]
}
</lang-strings>
