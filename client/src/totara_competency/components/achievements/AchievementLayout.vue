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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_competency
-->

<template>
  <Grid
    class="tui-achievementLayout"
    :class="{ 'tui-achievementLayout--borderless': noBorders }"
    :stack-at="750"
  >
    <GridItem :units="2">
      <div class="tui-achievementLayout__left">
        <slot name="left" />
      </div>
    </GridItem>
    <GridItem :units="10">
      <slot name="right" />
    </GridItem>
  </Grid>
</template>

<script>
// Components
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';

export default {
  components: {
    Grid,
    GridItem,
  },

  props: {
    noBorders: {
      type: Boolean,
    },
  },
};
</script>
