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
  @module container_workspace
-->

<template>
  <div class="tui-workspaceLibraryTab">
    <ContributionBaseContent
      :units="units"
      :loading="$apollo.loading"
      :loading-more="loadingMore"
      :cards="contribution.cards"
      :total-cards="contribution.cursor.total"
      :show-heading="false"
      :grid-direction="gridDirection"
      :show-footnotes="includeFootnotes"
      :is-load-more-visible="isLoadMoreVisible"
      @scrolled-to-bottom="scrolledToBottom"
      @load-more="loadMore"
    >
      <template v-slot:filters>
        <ContributionFilter
          component="container_workspace"
          area="library"
          :has-bottom-bar="true"
          :value="filterValue"
          :show-access="false"
          :show-type="true"
          :show-topic="true"
          :show-sort="true"
          :show-search="true"
          :search-placeholder="$str('search_library', 'container_workspace')"
          class="tui-workspaceLibraryTab__filter"
          @type="filterType"
          @topic="filterTopic"
          @search="filterSearch"
          @sort="filterSort"
        />
      </template>
    </ContributionBaseContent>
  </div>
</template>

<script>
import ContributionBaseContent from 'totara_engage/components/contribution/BaseContent';
import ContributionFilter from 'totara_engage/components/contribution/Filter';
import { UrlSourceType } from 'totara_engage/index';

// Mixins
import ContributionMixin from 'totara_engage/mixins/contribution_mixin';

// GraphQL
import getWorkspaceInteractor from 'container_workspace/graphql/workspace_interactor';
import sharedCards from 'container_workspace/graphql/shared_cards';

export default {
  components: {
    ContributionBaseContent,
    ContributionFilter,
  },

  mixins: [ContributionMixin],

  props: {
    workspaceId: {
      type: [Number, String],
      required: true,
    },
  },

  data() {
    return {
      interactor: {},
    };
  },

  computed: {
    canContribute() {
      return this.interactor.can_share_resources;
    },
  },

  created() {
    // Overwrite values defined in ContributionMixin.
    this.filterValue = Object.assign({}, this.filterValue, {
      sort: 5,
    });
    this.includeFootnotes = true;
  },

  apollo: {
    interactor: {
      query: getWorkspaceInteractor,
      variables() {
        return {
          workspace_id: this.workspaceId,
        };
      },
    },
    contribution: {
      query: sharedCards,
      fetchPolicy: 'network-only',
      variables() {
        return Object.assign({}, this.filterValue, {
          workspace_id: this.workspaceId,
          area: 'library',
          include_footnotes: true,
          footnotes_type: 'shared',
          footnotes_item_id: this.workspaceId,
          footnotes_area: 'LIBRARY',
          footnotes_component: 'container_workspace',
          source: UrlSourceType.workspace(this.workspaceId),
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
    $_addContributeCard(cards) {
      let contributeCard = {
        instanceid: this.workspaceId,
        component: 'WorkspaceContributeCard',
        tuicomponent:
          'container_workspace/components/card/WorkspaceContributeCard',
        user: {},
      };
      return Array.prototype.concat([contributeCard], cards);
    },

    adderOpen() {
      this.showAdder = true;
    },

    adderCancelled() {
      this.showAdder = false;
    },

    adderUpdate(selection) {
      this.addedItems = selection;
      this.showAdder = false;
    },
  },
};
</script>

<lang-strings>
{
  "container_workspace": [
    "search_library"
  ]
}
</lang-strings>
