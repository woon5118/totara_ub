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
  @package totara_core
-->

<template>
  <div class="tui-sidePanel-example">
    <Grid class="tui-grid--animated">
      <GridItem :units="gridUnitsLeft">
        <SidePanel
          :grow-height-on-scroll="false"
          :initially-open="true"
          :overflows="false"
          @sidepanel-expanding="expandRequestLeft"
          @sidepanel-collapsing="collapseRequestLeft"
        >
          <template>
            <Grid
              direction="vertical"
              :use-vertical-gap="false"
              :style="{
                height: '100%',
              }"
            >
              <GridItem :units="3" :grows="true">
                <h3>Title (original left)</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <h4>Comments</h4>
              </GridItem>
              <GridItem :units="8" :overflows="true" :shrinks="true">
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                  Suspendisse sit amet erat ex. Sed ac felis auctor, molestie
                  orci eget, semper tortor. Curabitur non elementum nisi.
                </p>

                <p>
                  Donec sit amet venenatis ligula, at congue massa. In ac dolor
                  ante. Mauris faucibus, nulla consectetur scelerisque
                  efficitur, risus leo pharetra mi, non vehicula tellus elit
                  eget massa. Quisque feugiat eros et aliquam tempus.
                </p>

                <p>
                  Maecenas ut ornare sapien. Nulla sed rutrum ante. Pellentesque
                  habitant morbi tristique senectus et netus et malesuada fames
                  ac turpis egestas.
                </p>

                <p>
                  In turpis purus, feugiat sed commodo vitae, hendrerit in
                  tortor. Ut sed risus dolor. Vestibulum sed sapien ultrices
                  ipsum interdum facilisis nec non augue. Nulla et tellus id
                  ipsum congue aliquet in a quam.
                </p>

                <p>
                  Aenean cursus dolor vitae arcu egestas, vel interdum justo
                  bibendum. Cras convallis nulla sit amet eros interdum, a
                  aliquet metus fermentum. Fusce dictum est libero, vitae
                  rhoncus lectus lacinia sed.
                </p>

                <p>
                  Nunc ullamcorper eros a arcu hendrerit laoreet. Phasellus
                  elementum feugiat orci, sed consequat mauris luctus vel.
                  Praesent id dolor id lorem ultrices feugiat.
                </p>

                <p>
                  Praesent posuere nunc ipsum, egestas vestibulum odio ultrices
                  in. Duis suscipit lorem nisi, sed viverra odio blandit sit
                  amet. Fusce ultrices nibh odio, nec eleifend nibh efficitur
                  in.
                </p>

                <p>
                  Quisque eget lectus pulvinar, ornare enim sit amet, interdum
                  ligula. Donec lobortis magna ut malesuada tincidunt. Aenean
                  consectetur augue sit amet nulla blandit, eget feugiat eros
                  gravida.
                </p>

                <p>
                  Nulla pulvinar commodo congue. Nam sed sem at enim gravida
                  feugiat. Nunc eget lorem feugiat sem iaculis suscipit id ut
                  nisl. Nunc in ullamcorper nulla. Praesent eu sem feugiat est
                  faucibus dapibus. Fusce fermentum ipsum vitae nulla viverra
                  imperdiet.
                </p>
              </GridItem>
              <GridItem :units="1" :grows="true">
                <p>Some tags....</p>
              </GridItem>
            </Grid>
          </template>
        </SidePanel>
      </GridItem>

      <GridItem :units="gridUnitsCenter" :shrinks="true">
        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse
          sit amet erat ex. Sed ac felis auctor, molestie orci eget, semper
          tortor. Curabitur non elementum nisi. Donec sit amet venenatis ligula,
          at congue massa. In ac dolor ante. Mauris faucibus, nulla consectetur
          scelerisque efficitur, risus leo pharetra mi, non vehicula tellus elit
          eget massa. Quisque feugiat eros et aliquam tempus.
        </p>

        <p>
          Maecenas ut ornare sapien. Nulla sed rutrum ante. Pellentesque
          habitant morbi tristique senectus et netus et malesuada fames ac
          turpis egestas. In turpis purus, feugiat sed commodo vitae, hendrerit
          in tortor. Ut sed risus dolor. Vestibulum sed sapien ultrices ipsum
          interdum facilisis nec non augue. Nulla et tellus id ipsum congue
          aliquet in a quam. Aenean cursus dolor vitae arcu egestas, vel
          interdum justo bibendum. Cras convallis nulla sit amet eros interdum,
          a aliquet metus fermentum. Fusce dictum est libero, vitae rhoncus
          lectus lacinia sed. Nunc ullamcorper eros a arcu hendrerit laoreet.
          Phasellus elementum feugiat orci, sed consequat mauris luctus vel.
          Praesent id dolor id lorem ultrices feugiat.
        </p>

        <p>
          Praesent posuere nunc ipsum, egestas vestibulum odio ultrices in. Duis
          suscipit lorem nisi, sed viverra odio blandit sit amet. Fusce ultrices
          nibh odio, nec eleifend nibh efficitur in. Quisque eget lectus
          pulvinar, ornare enim sit amet, interdum ligula. Donec lobortis magna
          ut malesuada tincidunt. Aenean consectetur augue sit amet nulla
          blandit, eget feugiat eros gravida. Nulla pulvinar commodo congue. Nam
          sed sem at enim gravida feugiat. Nunc eget lorem feugiat sem iaculis
          suscipit id ut nisl. Nunc in ullamcorper nulla. Praesent eu sem
          feugiat est faucibus dapibus. Fusce fermentum ipsum vitae nulla
          viverra imperdiet.
        </p>

        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse
          sit amet erat ex. Sed ac felis auctor, molestie orci eget, semper
          tortor. Curabitur non elementum nisi. Donec sit amet venenatis ligula,
          at congue massa. In ac dolor ante. Mauris faucibus, nulla consectetur
          scelerisque efficitur, risus leo pharetra mi, non vehicula tellus elit
          eget massa. Quisque feugiat eros et aliquam tempus.
        </p>

        <p>
          Maecenas ut ornare sapien. Nulla sed rutrum ante. Pellentesque
          habitant morbi tristique senectus et netus et malesuada fames ac
          turpis egestas. In turpis purus, feugiat sed commodo vitae, hendrerit
          in tortor. Ut sed risus dolor. Vestibulum sed sapien ultrices ipsum
          interdum facilisis nec non augue. Nulla et tellus id ipsum congue
          aliquet in a quam. Aenean cursus dolor vitae arcu egestas, vel
          interdum justo bibendum. Cras convallis nulla sit amet eros interdum,
          a aliquet metus fermentum. Fusce dictum est libero, vitae rhoncus
          lectus lacinia sed. Nunc ullamcorper eros a arcu hendrerit laoreet.
          Phasellus elementum feugiat orci, sed consequat mauris luctus vel.
          Praesent id dolor id lorem ultrices feugiat.
        </p>

        <p>
          Praesent posuere nunc ipsum, egestas vestibulum odio ultrices in. Duis
          suscipit lorem nisi, sed viverra odio blandit sit amet. Fusce ultrices
          nibh odio, nec eleifend nibh efficitur in. Quisque eget lectus
          pulvinar, ornare enim sit amet, interdum ligula. Donec lobortis magna
          ut malesuada tincidunt. Aenean consectetur augue sit amet nulla
          blandit, eget feugiat eros gravida. Nulla pulvinar commodo congue. Nam
          sed sem at enim gravida feugiat. Nunc eget lorem feugiat sem iaculis
          suscipit id ut nisl. Nunc in ullamcorper nulla. Praesent eu sem
          feugiat est faucibus dapibus. Fusce fermentum ipsum vitae nulla
          viverra imperdiet.
        </p>

        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse
          sit amet erat ex. Sed ac felis auctor, molestie orci eget, semper
          tortor. Curabitur non elementum nisi. Donec sit amet venenatis ligula,
          at congue massa. In ac dolor ante. Mauris faucibus, nulla consectetur
          scelerisque efficitur, risus leo pharetra mi, non vehicula tellus elit
          eget massa. Quisque feugiat eros et aliquam tempus.
        </p>

        <p>
          Maecenas ut ornare sapien. Nulla sed rutrum ante. Pellentesque
          habitant morbi tristique senectus et netus et malesuada fames ac
          turpis egestas. In turpis purus, feugiat sed commodo vitae, hendrerit
          in tortor. Ut sed risus dolor. Vestibulum sed sapien ultrices ipsum
          interdum facilisis nec non augue. Nulla et tellus id ipsum congue
          aliquet in a quam. Aenean cursus dolor vitae arcu egestas, vel
          interdum justo bibendum. Cras convallis nulla sit amet eros interdum,
          a aliquet metus fermentum. Fusce dictum est libero, vitae rhoncus
          lectus lacinia sed. Nunc ullamcorper eros a arcu hendrerit laoreet.
          Phasellus elementum feugiat orci, sed consequat mauris luctus vel.
          Praesent id dolor id lorem ultrices feugiat.
        </p>

        <p>
          Praesent posuere nunc ipsum, egestas vestibulum odio ultrices in. Duis
          suscipit lorem nisi, sed viverra odio blandit sit amet. Fusce ultrices
          nibh odio, nec eleifend nibh efficitur in. Quisque eget lectus
          pulvinar, ornare enim sit amet, interdum ligula. Donec lobortis magna
          ut malesuada tincidunt. Aenean consectetur augue sit amet nulla
          blandit, eget feugiat eros gravida. Nulla pulvinar commodo congue. Nam
          sed sem at enim gravida feugiat. Nunc eget lorem feugiat sem iaculis
          suscipit id ut nisl. Nunc in ullamcorper nulla. Praesent eu sem
          feugiat est faucibus dapibus. Fusce fermentum ipsum vitae nulla
          viverra imperdiet.
        </p>
      </GridItem>

      <GridItem :units="gridUnitsRight">
        <SidePanel
          direction="rtl"
          :overflows="false"
          :animated="false"
          @sidepanel-expanding="expandRequestRight"
          @sidepanel-collapsing="collapseRequestRight"
        >
          <template>
            <aside>
              <Grid
                direction="vertical"
                :use-vertical-gap="false"
                :style="{
                  height: '100%',
                }"
              >
                <GridItem :units="3" :grows="true">
                  <h3>Title (original right)</h3>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                  </p>
                  <h4>Comments</h4>
                </GridItem>
                <GridItem :units="8" :overflows="true" :shrinks="true">
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Suspendisse sit amet erat ex. Sed ac felis auctor, molestie
                    orci eget, semper tortor. Curabitur non elementum nisi.
                  </p>

                  <p>
                    Donec sit amet venenatis ligula, at congue massa. In ac
                    dolor ante. Mauris faucibus, nulla consectetur scelerisque
                    efficitur, risus leo pharetra mi, non vehicula tellus elit
                    eget massa. Quisque feugiat eros et aliquam tempus.
                  </p>

                  <p>
                    Maecenas ut ornare sapien. Nulla sed rutrum ante.
                    Pellentesque habitant morbi tristique senectus et netus et
                    malesuada fames ac turpis egestas.
                  </p>

                  <p>
                    In turpis purus, feugiat sed commodo vitae, hendrerit in
                    tortor. Ut sed risus dolor. Vestibulum sed sapien ultrices
                    ipsum interdum facilisis nec non augue. Nulla et tellus id
                    ipsum congue aliquet in a quam.
                  </p>

                  <p>
                    Aenean cursus dolor vitae arcu egestas, vel interdum justo
                    bibendum. Cras convallis nulla sit amet eros interdum, a
                    aliquet metus fermentum. Fusce dictum est libero, vitae
                    rhoncus lectus lacinia sed.
                  </p>

                  <p>
                    Nunc ullamcorper eros a arcu hendrerit laoreet. Phasellus
                    elementum feugiat orci, sed consequat mauris luctus vel.
                    Praesent id dolor id lorem ultrices feugiat.
                  </p>

                  <p>
                    Praesent posuere nunc ipsum, egestas vestibulum odio
                    ultrices in. Duis suscipit lorem nisi, sed viverra odio
                    blandit sit amet. Fusce ultrices nibh odio, nec eleifend
                    nibh efficitur in.
                  </p>

                  <p>
                    Quisque eget lectus pulvinar, ornare enim sit amet, interdum
                    ligula. Donec lobortis magna ut malesuada tincidunt. Aenean
                    consectetur augue sit amet nulla blandit, eget feugiat eros
                    gravida.
                  </p>

                  <p>
                    Nulla pulvinar commodo congue. Nam sed sem at enim gravida
                    feugiat. Nunc eget lorem feugiat sem iaculis suscipit id ut
                    nisl. Nunc in ullamcorper nulla. Praesent eu sem feugiat est
                    faucibus dapibus. Fusce fermentum ipsum vitae nulla viverra
                    imperdiet.
                  </p>
                </GridItem>
                <GridItem :units="1" :grows="true">
                  <p>Some tags....</p>
                </GridItem>
              </Grid>
            </aside>
          </template>
        </SidePanel>
      </GridItem>
    </Grid>
  </div>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import SidePanel from 'totara_core/components/sidepanel/SidePanel';

export default {
  components: {
    Grid,
    GridItem,
    SidePanel,
  },
  data() {
    return {
      gridUnitsLeft: 2,
      gridUnitsCenter: 8,
      gridUnitsRight: 2,
    };
  },
  methods: {
    // open/close for "left" sidepanel
    expandRequestLeft: function() {
      this.gridUnitsLeft = 5;
      this.gridUnitsCenter = 2;
    },
    collapseRequestLeft: function() {
      this.gridUnitsLeft = 2;
      this.gridUnitsCenter = 5;
    },
    // open/close for "right" sidepanel
    expandRequestRight: function() {
      this.gridUnitsRight = 5;
      this.gridUnitsCenter = 2;
    },
    collapseRequestRight: function() {
      this.gridUnitsRight = 2;
      this.gridUnitsCenter = 5;
    },
  },
};
</script>

<style lang="scss">
.tui-sidePanel-example {
  height: 100%;

  .tui-grid--animated > .tui-grid-item {
    transition: flex-basis ease-in 0.3s;
  }
}
</style>
