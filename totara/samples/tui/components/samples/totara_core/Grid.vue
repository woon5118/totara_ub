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
  <div class="tui-grid-example">
    <h3>Default Horizontal 12 column grid examples</h3>

    <p>
      12 items using 12/12 units. <code>use-horizontal-gap</code> is set to
      <code>false</code> to remove grid item gaps.
    </p>
    <Grid :use-horizontal-gap="false">
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <p>
      12 items using 12/12 units, switches to a stack when the element width is
      <code>&lt; 900px</code>.
    </p>
    <Grid :stack-at="900">
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <p>
      12 items using 12/12 units, switches to a stack when the viewport width is
      <code>&lt; 768px</code>.
    </p>
    <Grid :stack-at="768">
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem :units="4">4u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <p>
      3 items using 19/12 units (intentionally exceeding <code>maxUnits</code>).
      <code>use-vertical-gap</code> is set to <code>false</code> to remove grid
      item gaps.
    </p>

    <Grid :use-vertical-gap="false">
      <GridItem>1u</GridItem>
      <GridItem :units="9">9u</GridItem>
      <GridItem :units="9">9u (excess wraps)</GridItem>
    </Grid>

    <br />

    <p>
      19 items using 19/12 units (intentionally exceeding
      <code>maxUnits</code> and logical maximum number of items).
    </p>

    <Grid>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <p>2 items using 10/12 units, no <code>grows</code> enabled.</p>
    <Grid>
      <GridItem :units="1">1u</GridItem>
      <GridItem :units="9">9u</GridItem>
    </Grid>

    <br />

    <p>
      2 items using 1+?/12 units, unspecified item has
      <code>grows</code> enabled.
    </p>
    <Grid>
      <GridItem :units="1">1u</GridItem>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), <code>grows</code> enabled</GridItem
      >
    </Grid>

    <br />

    <p>
      2 items using ?+2/12 units, unspecified item has
      <code>grows</code> enabled. This is an example of aligning GridItems to
      one side of the Grid.
    </p>
    <Grid>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), <code>grows</code> enabled</GridItem
      >
      <GridItem :units="2">2u</GridItem>
    </Grid>

    <br />

    <p>
      3 items using ?+?+?/12 units, unspecified items all have
      <code>grows</code> enabled.
    </p>
    <Grid>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), <code>grows</code> enabled</GridItem
      >
      <GridItem :grows="true"
        >unspecified units (defaults to 1), <code>grows</code> enabled</GridItem
      >
      <GridItem :grows="true"
        >unspecified units (defaults to 1), <code>grows</code> enabled</GridItem
      >
    </Grid>

    <br />

    <p>
      3 items using 19/12 units (exceeds <code>maxUnits</code>, so auto wraps).
    </p>
    <Grid>
      <GridItem>1u</GridItem>
      <GridItem :units="9">9u</GridItem>
      <GridItem :units="9">9u (excess wraps)</GridItem>
    </Grid>

    <br />

    <p>2 items using 1+?/12 units</p>
    <Grid>
      <GridItem :units="1">1u</GridItem>
      <GridItem>unspecified units (defaults to 1)</GridItem>
    </Grid>

    <br />

    <p>
      3 items using 2+?+2/12 units, middle grid item has
      <code>grows</code> enabled
    </p>
    <Grid>
      <GridItem :units="2">2u</GridItem>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), with
        <code>grows</code> enabled</GridItem
      >
      <GridItem :units="2">2u</GridItem>
    </Grid>

    <br />

    <p>
      3 items using ?+6+?/12 units, left and right grid items have
      <code>grows</code> enabled. This is an example of centering GridItems
      within the Grid.
    </p>
    <Grid>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), with
        <code>grows</code> enabled</GridItem
      >
      <GridItem :units="6">6u</GridItem>
      <GridItem :grows="true"
        >unspecified units (defaults to 1), with
        <code>grows</code> enabled</GridItem
      >
    </Grid>

    <br />

    <p>
      2 items using ?+6/12 units, unspecified item has
      <code>shrinks</code> disabled, 6u item has <code>grows</code> and
      <code>overflows</code> enabled.
    </p>
    <Grid>
      <GridItem :shrinks="false"
        >unspecified units (defaults to 1),
        <code>shrinks</code> disabled.</GridItem
      >
      <GridItem :units="6" :grows="true" :overflows="true">
        <img :src="imageData" alt="" width="1200" height="32" />
        <p>
          6u with wide image, <code>grows</code> and
          <code>overflows</code> enabled.
        </p>
      </GridItem>
    </Grid>

    <br />

    <p>3 items using 12/12 units, items are re-ordered</p>
    <Grid>
      <GridItem :units="6" :order="2">item 1 (re-ordered 2)</GridItem>
      <GridItem :units="3" :order="1">item 2 (re-ordered 1)</GridItem>
      <GridItem :units="3" :order="3">item 3 (re-ordered 3)</GridItem>
    </Grid>

    <br />

    <h3>Non-default Horizontal custom column grid examples</h3>

    <p>
      "9 unit grid" (changed default <code>maxUnits</code> value) with 9 items
      using 9/9 units.
    </p>
    <Grid max-units="9">
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <p>
      "9 unit grid" (changed default <code>maxUnits</code> value) with 6 items
      using 9/9 units.
    </p>
    <Grid max-units="9">
      <GridItem :units="3">3u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
      <GridItem>1u</GridItem>
    </Grid>

    <br />

    <br /><br />

    <h3>Flex re-ordering examples</h3>
    <p>
      3 items using 12/12 units, all items are re-ordered with
      <code>:order</code>
    </p>

    <br />

    <Grid>
      <GridItem :units="4" :order="2">item 1 (re-ordered 2)</GridItem>
      <GridItem :units="4" :order="3">item 2 (re-ordered 3)</GridItem>
      <GridItem :units="4" :order="1">item 3 (re-ordered 1)</GridItem>
    </Grid>

    <br />

    <Grid>
      <GridItem :units="4" :order="2">item 1 (re-ordered 2)</GridItem>
      <GridItem :units="4" :order="1">item 2 (re-ordered 1)</GridItem>
      <GridItem :units="4" :order="3">item 3 (re-ordered 3)</GridItem>
    </Grid>

    <br />

    <Grid>
      <GridItem :units="4" :order="3">item 1 (re-ordered 3)</GridItem>
      <GridItem :units="4" :order="2">item 2 (re-ordered 2)</GridItem>
      <GridItem :units="4" :order="1">item 3 (re-ordered 1)</GridItem>
    </Grid>

    <br />

    <Grid>
      <GridItem :units="4" :order="3">item 1 (re-ordered 3)</GridItem>
      <GridItem :units="4" :order="1">item 2 (re-ordered 1)</GridItem>
      <GridItem :units="4" :order="2">item 3 (re-ordered 2)</GridItem>
    </Grid>

    <br />

    <p>
      3 items using 12/12 units, only some items are re-ordered with
      <code>:order</code>. If re-ordering, it is recommended to order all items,
      not doing so can have unexpected results.
    </p>
    <Grid>
      <GridItem :units="4">item 1 (not re-ordered)</GridItem>
      <GridItem :units="4" :order="3">item 2 (re-ordered 3)</GridItem>
      <GridItem :units="4" :order="2">item 3 (re-ordered 2)</GridItem>
    </Grid>

    <br />

    <Grid>
      <GridItem :units="4">item 1 (not re-ordered)</GridItem>
      <GridItem :units="4" :order="1">item 2 (re-ordered 1)</GridItem>
      <GridItem :units="4">item 3 (not re-ordered)</GridItem>
    </Grid>

    <br /><br />
    <h3>Vertical grid examples</h3>

    <p>
      2 items using 10/12 units, no explicit <code>height</code> set to show
      lack of unit distribution. <code>use-vertical-gap</code> is set to
      <code>false</code> to remove grid item gaps.
    </p>
    <Grid direction="vertical" :use-vertical-gap="false">
      <GridItem :units="1">1u</GridItem>
      <GridItem :units="9">9u</GridItem>
    </Grid>

    <br />

    <p>
      2 items using 10/12 units, explicit <code>height</code> set to show unit
      distribution, no <code>grows</code> enabled.
    </p>
    <Grid direction="vertical" style="height: 350px;">
      <GridItem :units="1">1u</GridItem>
      <GridItem :units="9">9u</GridItem>
    </Grid>

    <br />

    <p>
      2 items using 10/12 units, explicit <code>height</code> set to show unit
      distribution, <code>grows</code> enabled.
    </p>
    <Grid direction="vertical" style="height: 300px;">
      <GridItem :units="1">1u</GridItem>
      <GridItem :units="9" :grows="true"
        >9u, <code>grows</code> enabled</GridItem
      >
    </Grid>

    <br />

    <p>
      3 items using 12/12 units, explicit <code>height</code> set to show unit
      distribution
    </p>
    <Grid direction="vertical" style="height: 350px;">
      <GridItem :units="1">1u</GridItem>
      <GridItem :units="9">9u</GridItem>
      <GridItem :units="2">2u</GridItem>
    </Grid>

    <br /><br />
    <h3>Nested grid examples</h3>

    <p>
      3 items using 12/12 units, middle grid item contains a nested grid using
      4/12 units, <code>grows</code> enabled.
    </p>
    <Grid>
      <GridItem :units="1">
        <img :src="imageData" alt="" width="32" height="32" />
        <p>1u with image</p>
      </GridItem>
      <GridItem :units="6">
        <Grid gutter-class="tui-grid-gutter--2">
          <GridItem :units="2">
            2u nested grid item
          </GridItem>
          <GridItem :units="2" :grows="true">
            2u nested grid item, <code>grows</code> enabled
          </GridItem>
        </Grid>
      </GridItem>
      <GridItem :units="5">
        5u
      </GridItem>
    </Grid>

    <br />

    <p>
      3 items using 12/12 units, each grid item contains a nested vertical grid.
      Each nested vertical grid has 3 items using 3+?/12 units and each middle
      grid item has <code>grows</code> and <code>overflows</code> enabled.
    </p>
    <Grid :stack-at="768">
      <GridItem :units="4">
        <p>
          4u with nested vertical grid, <code>height</code> set to show unit
          distribution
        </p>
        <Grid direction="vertical">
          <GridItem :units="2">2u nested grid item</GridItem>
          <GridItem :grows="true">
            unspecified units (defaults to 1) nested grid item,
            <code>grows</code> enabled
          </GridItem>
          <GridItem :units="1">1u nested grid item</GridItem>
        </Grid>
      </GridItem>
      <GridItem :units="4">
        <p>
          4u with nested vertical grid, <code>height</code> set to show unit
          distribution
        </p>
        <Grid direction="vertical">
          <GridItem :units="2">2u nested grid item</GridItem>
          <GridItem :grows="true">
            unspecified units (defaults to 1) nested grid item,
            <code>grows</code> enabled
          </GridItem>
          <GridItem :units="1">1u nested grid item</GridItem>
        </Grid>
      </GridItem>
      <GridItem :units="4">
        <p>
          4u with nested vertical grid, <code>height</code> set to show unit
          distribution
        </p>
        <Grid direction="vertical" style="height: 400px;">
          <GridItem :units="2">2u nested grid item</GridItem>
          <GridItem :grows="true" :overflows="true">
            <p>
              unspecified units (defaults to 1) nested grid item with tall
              image, <code>grows</code> and <code>overflows</code> enabled
            </p>
            <img :src="imageData" alt="" width="32" height="1200" />
          </GridItem>
          <GridItem :units="1">1u nested grid item</GridItem>
        </Grid>
      </GridItem>
    </Grid>

    <br />

    <p>
      3 items using 12/12 units (e.g.; 3-column layout), order changed, side
      items contain sub-grids (e.g.; Blocks). Centre item contains several Grids
      (e.g.; tile layout).
    </p>
    <p>
      <em
        >For IE11, the nested vertical Grids will not display correctly without
        explicit heights set where height distribution is wanted - this is a
        known limitation of flexbox support in IE11, so use with caution.</em
      >
    </p>
    <Grid :stack-at="768">
      <GridItem :units="3" :order="1">
        <Grid direction="vertical" class="tui-grid_block">
          <GridItem :units="2">
            <h3>Block 1 header</h3>
          </GridItem>
          <GridItem :grows="true">
            <p>
              Veggies es bonus vobis, proinde vos postulo essum magis kohlrabi
              welsh onion daikon amaranth tatsoi tomatillo melon azuki bean
              garlic.
            </p>
            <p>
              Gumbo beet greens corn soko endive gumbo gourd. Parsley shallot
              courgette tatsoi pea sprouts fava bean collard greens dandelion
              okra wakame tomato. Dandelion cucumber earthnut pea peanut soko
              zucchini.
            </p>
          </GridItem>
          <GridItem :units="2">
            <p>Block footer</p>
          </GridItem>
        </Grid>

        <Grid direction="vertical" class="tui-grid_block">
          <GridItem :units="2">
            <h3>Block 2 header</h3>
          </GridItem>
          <GridItem :grows="true">
            <p>
              Veggies es bonus vobis, proinde vos postulo essum magis kohlrabi
              welsh onion daikon amaranth tatsoi tomatillo melon azuki bean
              garlic.
            </p>
          </GridItem>
          <GridItem :units="2">
            <p>Block footer</p>
          </GridItem>
        </Grid>
      </GridItem>
      <GridItem :units="3" :order="3">
        <Grid direction="vertical" class="tui-grid_block">
          <GridItem :units="2">
            <h3>Block 3 header</h3>
          </GridItem>
          <GridItem :grows="true">
            <p>
              Gumbo beet greens corn soko endive gumbo gourd. Parsley shallot
              courgette tatsoi pea sprouts fava bean collard greens dandelion
              okra wakame tomato. Dandelion cucumber earthnut pea peanut soko
              zucchini.
            </p>
          </GridItem>
          <GridItem :units="2">
            <p>Block footer</p>
          </GridItem>
        </Grid>

        <Grid direction="vertical" class="tui-grid_block">
          <GridItem :units="2">
            <h3>Block 4 header</h3>
          </GridItem>
          <GridItem :grows="true">
            <p>
              Nori grape silver beet broccoli kombu beet greens fava bean potato
              quandong celery. Bunya nuts black-eyed pea prairie turnip leek
              lentil turnip greens parsnip.
            </p>
          </GridItem>
          <GridItem :units="2">
            <p>Block footer</p>
          </GridItem>
        </Grid>
      </GridItem>
      <GridItem :units="6" :order="2">
        <Grid :stack-at="400" class="tui-grid-example_row-of-tiles">
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
        </Grid>

        <Grid :stack-at="400" class="tui-grid-example_row-of-tiles">
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
        </Grid>

        <Grid :stack-at="400" class="tui-grid-example_row-of-tiles">
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
          <GridItem :units="4">4u</GridItem>
        </Grid>
      </GridItem>
    </Grid>
  </div>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

export default {
  components: {
    Grid,
    GridItem,
  },
  data() {
    return {
      imageData:
        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mM03f3sPwAF2gLXke5VKgAAAABJRU5ErkJggg==',
    };
  },
};
</script>
<style lang="scss">
.tui-grid-example .tui-grid {
  background-color: rgba(255, 0, 0, 0.2);
  * {
    background-color: rgba(0, 255, 0, 0.2);
  }
  * * {
    background-color: rgba(0, 0, 255, 0.2);
  }
  * * * {
    background-color: rgba(255, 0, 255, 0.2);
  }
  * * * * {
    background-color: rgba(0, 255, 255, 0.2);
  }
  * * * * * {
    background-color: rgba(255, 255, 0, 0.2);
  }

  .tui-grid-item--first {
    background: yellow;
  }
}
.tui-grid-example_row-of-tiles > .tui-grid-item {
  min-height: 150px;
}
</style>
