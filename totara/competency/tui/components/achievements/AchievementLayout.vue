<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
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
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

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
