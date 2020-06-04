<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package theme_ventura
-->

<template>
  <div class="tui-layoutTwoColumn">
    <Responsive
      v-slot="slotProps"
      :breakpoints="[
        { name: 'small', boundaries: [0, 764] },
        { name: 'medium', boundaries: [765, 1192] },
        { name: 'large', boundaries: [1193, 1672] },
      ]"
      @responsive-resize="resize"
    >
      <div v-if="slotProps.currentBoundaryName === 'small'">
        <Grid direction="vertical">
          <GridItem>
            <h3 class="tui-layoutTwoColumn__heading">
              <slot name="page-title" />
            </h3>
            <slot name="right" />
          </GridItem>
          <GridItem>
            <slot name="left" />
          </GridItem>
        </Grid>
      </div>
      <!-- /small -->
      <div v-if="slotProps.currentBoundaryName === 'medium'">
        <Grid direction="horizontal">
          <GridItem :units="9" :order="2">
            <h3 class="tui-layoutTwoColumn__heading">
              <slot name="page-title" />
            </h3>
            <!-- first in DOM order for screenreaders, re-ordered visually to be
                in the center with flexbox order -->
            <slot name="right" :units="9" />
          </GridItem>
          <GridItem :units="3" :order="1">
            <slot name="left" :units="3" />
          </GridItem>
        </Grid>
      </div>
      <!-- /medium -->
      <div v-if="slotProps.currentBoundaryName === 'large'">
        <Grid direction="horizontal">
          <GridItem :units="10" :order="2">
            <h3 class="tui-layoutTwoColumn__heading">
              <slot name="page-title" />
            </h3>
            <!-- first in DOM order for screenreaders, re-ordered visually to be
                in the center with flexbox order -->
            <slot name="right" :units="10" />
          </GridItem>
          <GridItem :units="2" :order="1">
            <slot name="left" :units="2" />
          </GridItem>
        </Grid>
      </div>
      <!-- /large -->
    </Responsive>
  </div>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Responsive from 'totara_core/components/responsive/Responsive';

export default {
  components: {
    Grid,
    GridItem,
    Responsive,
  },

  methods: {
    resize(boundary) {
      if (boundary === 'small') {
        this.$emit('direction-change', 'vertical');
        return;
      }

      this.$emit('direction-change', 'horizontal');
    },
  },
};
</script>
