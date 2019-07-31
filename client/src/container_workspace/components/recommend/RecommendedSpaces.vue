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
  <div class="tui-recommendedSpaces">
    <div class="tui-recommendedSpaces__head">
      <h2 class="tui-recommendedSpaces__head__title">
        <span>{{ $str('recommended_spaces', 'container_workspace') }}</span>
        <Spinner v-if="$apollo.loading" />
      </h2>

      <a
        :href="$url('/container/type/workspace/spaces.php')"
        class="tui-recommendedSpaces__head__link"
      >
        {{ $str('view_all_spaces', 'container_workspace') }}
      </a>
    </div>

    <SpaceCardsGrid
      v-if="!$apollo.loading && 0 !== workspaces.length"
      :max-grid-units="maxGridUnits"
      :workspaces="workspaces"
      class="tui-recommendedSpaces__grid"
      @join-workspace="joinWorkspace"
    />
  </div>
</template>

<script>
import Spinner from 'tui/components/icons/common/Spinner';
import SpaceCardsGrid from 'container_workspace/components/grid/SpaceCardsGrid';

// GraphQL Queries
import recommendedSpaces from 'ml_recommender/graphql/get_recommended_user_workspaces';

export default {
  components: {
    Spinner,
    SpaceCardsGrid,
  },

  props: {
    maxGridUnits: {
      type: [Number, String],
      required: true,
    },
  },

  apollo: {
    workspaces: {
      query: recommendedSpaces,
      fetchPolicy: 'network-only',
    },
  },

  data() {
    return {
      workspaces: [],
    };
  },

  methods: {
    /**
     *
     * @param {Number} workspace_id
     */
    joinWorkspace({ workspace_id }) {
      // After everything, we just need to emit an event up tot he parent.
      this.$emit('join-workspace', workspace_id);
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "recommended_spaces",
      "view_all_spaces"
    ]
  }
</lang-strings>
