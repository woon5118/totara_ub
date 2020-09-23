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
  <div class="tui-spaceCardsGrid">
    <Grid
      v-for="({ items: row }, i) in rows"
      :key="i"
      :max-units="maxGridUnits"
      class="tui-spaceCardsGrid__row"
    >
      <GridItem
        v-for="({ id, url, image, name, interactor }, x) in row"
        :key="x"
        :units="workspaceUnits"
        class="tui-spaceCardsGrid__card"
      >
        <OriginalSpaceCard
          :workspace-id="id"
          :url="url"
          :image="image"
          :title="name"
          :joined="interactor.joined"
          :join-able="interactor.can_join"
          :has-requested-to-join="interactor.has_requested_to_join"
          :request-to-join-able="interactor.can_request_to_join"
          :owned="interactor.own"
          @request-to-join-workspace="
            $emit('request-to-join-workspace', $event)
          "
          @join-workspace="$emit('join-workspace', $event)"
          @leave-workspace="$emit('leave-workspace', $event)"
        />
      </GridItem>
    </Grid>
  </div>
</template>

<script>
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import { calculateRow } from 'totara_engage/grid';
import OriginalSpaceCard from 'container_workspace/components/card/OriginalSpaceCard';

export default {
  components: {
    OriginalSpaceCard,
    Grid,
    GridItem,
  },

  props: {
    maxGridUnits: {
      type: [Number, String],
      required: true,
    },

    workspaces: {
      type: Array,
      required: true,
    },

    workspaceUnits: {
      type: [Number, String],
      default: 2,
    },
  },

  computed: {
    rows() {
      return calculateRow(this.workspaces, this.workspacesPerRow);
    },

    /**
     * Given the maximum units and the number of units per card. We can figure out the number of cards per row
     * quite easily.
     * @return {Number}
     */
    workspacesPerRow() {
      return Math.floor(this.maxGridUnits / this.workspaceUnits);
    },
  },
};
</script>

<style lang="scss">
.tui-spaceCardsGrid {
  &__row {
    // Override the margin.
    &.tui-grid {
      margin-bottom: var(--gap-4);
    }
  }

  &__card {
    height: var(--totara-engage-card-height);
  }
}
</style>
