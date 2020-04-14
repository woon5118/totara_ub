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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    Available gap variables

    <SamplesExample>
      <Table :data="gaps">
        <template v-slot:header-row>
          <HeaderCell size="7">Variables</HeaderCell>
          <HeaderCell size="9">Example</HeaderCell>
        </template>
        <template v-slot:row="{ row }">
          <Cell size="7" column-header="Variables">
            {{ row.name }}
          </Cell>

          <Cell size="9" column-header="Example">
            <div class="tui-gapSamplesBox" :class="row.class" />
          </Cell>
        </template>
      </Table>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Cell,
    HeaderCell,
    SamplesCode,
    SamplesExample,
    Table,
  },

  data() {
    return {
      gaps: [
        {
          class: 'tui-gapSamplesBox--one',
          name: 'var(--tui-gap-1);',
        },
        {
          class: 'tui-gapSamplesBox--two',
          name: 'var(--tui-gap-2);',
        },
        {
          class: 'tui-gapSamplesBox--three',
          name: 'var(--tui-gap-3);',
        },
        {
          class: 'tui-gapSamplesBox--four',
          name: 'var(--tui-gap-4);',
        },
        {
          class: 'tui-gapSamplesBox--five',
          name: 'var(--tui-gap-5);',
        },
        {
          class: 'tui-gapSamplesBox--six',
          name: 'var(--tui-gap-6);',
        },
        {
          class: 'tui-gapSamplesBox--seven',
          name: 'var(--tui-gap-7);',
        },
        {
          class: 'tui-gapSamplesBox--eight',
          name: 'var(--tui-gap-8);',
        },
        {
          class: 'tui-gapSamplesBox--nine',
          name: 'var(--tui-gap-9);',
        },
        {
          class: 'tui-gapSamplesBox--ten',
          name: 'var(--tui-gap-10);',
        },
        {
          class: 'tui-gapSamplesBox--eleven',
          name: 'var(--tui-gap-11);',
        },
        {
          class: 'tui-gapSamplesBox--twelve',
          name: 'var(--tui-gap-12);',
        },
      ],
      codeTemplate: `<div class="tui-example__box">...</div>`,
      codeScript: `<style lang="scss">
  .tui-example__box {
    margin-left: var(--tui-gap-4);
  }
</style>`,
    };
  },
};
</script>

<style lang="scss">
.tui-gapSamplesBox {
  height: 30px;
  background: var(--tui-color-state);

  &--one {
    width: var(--tui-gap-1);
  }

  &--two {
    width: var(--tui-gap-2);
  }

  &--three {
    width: var(--tui-gap-3);
  }

  &--four {
    width: var(--tui-gap-4);
  }

  &--five {
    width: var(--tui-gap-5);
  }

  &--six {
    width: var(--tui-gap-6);
  }

  &--seven {
    width: var(--tui-gap-7);
  }

  &--eight {
    width: var(--tui-gap-8);
  }

  &--nine {
    width: var(--tui-gap-9);
  }

  &--ten {
    width: var(--tui-gap-10);
  }

  &--eleven {
    width: var(--tui-gap-11);
  }

  &--twelve {
    width: var(--tui-gap-12);
  }
}
</style>
