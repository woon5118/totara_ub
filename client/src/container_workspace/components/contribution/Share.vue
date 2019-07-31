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
  @module container_workspace
-->

<template>
  <EngageAdderModal
    :title="$str('workspace:add_library', 'container_workspace')"
    :open="showAdder"
    :cards="contribution.cards"
    :filter-value="filterValue"
    filter-component="container_workspace"
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
import shareWithRecipient from 'totara_engage/graphql/share_with_recipient';
import contributionCards from 'container_workspace/graphql/contribution_cards';

// Mixins
import ContributionMixin from 'totara_engage/mixins/contribution_mixin';

export default {
  components: {
    EngageAdderModal,
  },

  mixins: [ContributionMixin],

  props: {
    workspaceId: {
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
      query: contributionCards,
      fetchPolicy: 'network-only',
      variables() {
        return Object.assign({}, this.filterValue, {
          workspace_id: this.workspaceId,
          area: 'adder',
          include_footnotes: false,
        });
      },
      update({ contribution: { cursor, cards } }) {
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
          mutation: shareWithRecipient,
          refetchAll: false,
          refetchQueries: ['container_workspace_shared_cards'],
          variables: {
            items: selection.map(item => JSON.parse(item)),
            recipient: {
              instanceid: this.workspaceId,
              component: 'container_workspace',
              area: 'LIBRARY',
            },
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
  "container_workspace": [
    "workspace:add_library"
  ]
}
</lang-strings>
