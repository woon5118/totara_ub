<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_samples
-->

<template>
  <div>
    Available colour variables

    <SamplesExample>
      <Table :data="colours">
        <template v-slot:header-row>
          <HeaderCell size="7">Variables</HeaderCell>
          <HeaderCell size="4">Example</HeaderCell>
          <HeaderCell size="5">Note</HeaderCell>
        </template>
        <template v-slot:row="{ row }">
          <Cell size="7" column-header="Variables">
            {{ row.name }}
          </Cell>

          <Cell size="4" column-header="Example">
            <div class="tui-colourSamplesBox" :class="row.class" />
          </Cell>

          <Cell size="5" column-header="Example">
            {{ row.note }}
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
      colours: [
        {
          class: 'tui-colourSamplesBox--primary',
          name: 'var(--tui-color-primary);',
          note: 'No contrast dependency (No text on top)',
        },
        {
          class: 'tui-colourSamplesBox--secondary',
          name: 'var(--tui-color-secondary);',
        },
        {
          class: 'tui-colourSamplesBox--tertiary',
          name: 'var(--tui-color-tertiary);',
        },
        {
          class: '',
          name: 'State',
        },
        {
          class: 'tui-colourSamplesBox--state',
          name: 'var(--tui-color-state);',
        },
        {
          class: 'tui-colourSamplesBox--stateHover',
          name: 'var(--tui-color-state-hover);',
        },
        {
          class: 'tui-colourSamplesBox--stateFocus',
          name: 'var(--tui-color-state-focus);',
        },
        {
          class: 'tui-colourSamplesBox--stateActive',
          name: 'var(--tui-color-state-active);',
        },
        {
          class: 'tui-colourSamplesBox--stateHighlight',
          name: 'var(--tui-color-state-highlight);',
        },
        {
          class: 'tui-colourSamplesBox--stateDisabled',
          name: 'var(--tui-color-state-disabled);',
        },
        {
          class: '',
          name: 'Prompt',
        },
        {
          class: 'tui-colourSamplesBox--promptInfo',
          name: 'var(--tui-color-prompt-info);',
        },
        {
          class: 'tui-colourSamplesBox--promptInfoBg',
          name: 'var(--tui-color-prompt-info-bg);',
        },
        {
          class: 'tui-colourSamplesBox--promptAlert',
          name: 'var(--tui-color-prompt-alert);',
        },
        {
          class: 'tui-colourSamplesBox--promptAlertBg',
          name: 'var(--tui-color-prompt-alert-bg);',
        },
        {
          class: 'tui-colourSamplesBox--promptWarning',
          name: 'var(--tui-color-prompt-warning);',
        },
        {
          class: 'tui-colourSamplesBox--promptWarningBg',
          name: 'var(--tui-color-prompt-warning-bg);',
        },
        {
          class: 'tui-colourSamplesBox--promptSuccess',
          name: 'var(--tui-color-prompt-success);',
        },
        {
          class: 'tui-colourSamplesBox--promptSuccessBg',
          name: 'var(--tui-color-prompt-Success-bg);',
        },
        {
          class: '',
          name: 'Neutral',
        },
        {
          class: 'tui-colourSamplesBox--neutralOne',
          name: 'var(--tui-color-neutral-1);',
        },
        {
          class: 'tui-colourSamplesBox--neutralTwo',
          name: 'var(--tui-color-neutral-2);',
        },
        {
          class: 'tui-colourSamplesBox--neutralThree',
          name: 'var(--tui-color-neutral-3);',
        },
        {
          class: 'tui-colourSamplesBox--neutralFour',
          name: 'var(--tui-color-neutral-4);',
        },
        {
          class: 'tui-colourSamplesBox--neutralFive',
          name: 'var(--tui-color-neutral-5);',
        },
        {
          class: 'tui-colourSamplesBox--neutralSix',
          name: 'var(--tui-color-neutral-6);',
        },
        {
          class: 'tui-colourSamplesBox--neutralSeven',
          name: 'var(--tui-color-neutral-7);',
        },
        {
          class: '',
          name: 'Charts',
        },
        {
          class: 'tui-colourSamplesBox--chartOne',
          name: 'var(--tui-color-chart-background-1);',
        },
        {
          class: 'tui-colourSamplesBox--chartOneLight',
          name: 'var(--tui-color-chart-transparent-1);',
        },
        {
          class: 'tui-colourSamplesBox--chartTwo',
          name: 'var(--tui-color-chart-background-2);',
        },
        {
          class: 'tui-colourSamplesBox--chartTwoLight',
          name: 'var(--tui-color-chart-transparent-2);',
        },
        {
          class: 'tui-colourSamplesBox--chartThree',
          name: 'var(--tui-color-chart-background-3);',
        },
        {
          class: 'tui-colourSamplesBox--chartThreeLight',
          name: 'var(--tui-color-chart-transparent-3);',
        },
        {
          class: 'tui-colourSamplesBox--chartFour',
          name: 'var(--tui-color-chart-background-4);',
        },
        {
          class: 'tui-colourSamplesBox--chartFourLight',
          name: 'var(--tui-color-chart-transparent-4);',
        },
        {
          class: 'tui-colourSamplesBox--chartFive',
          name: 'var(--tui-color-chart-background-5);',
        },
        {
          class: 'tui-colourSamplesBox--chartFiveLight',
          name: 'var(--tui-color-chart-transparent-5);',
        },
        {
          class: 'tui-colourSamplesBox--chartSix',
          name: 'var(--tui-color-chart-background-6);',
        },
        {
          class: 'tui-colourSamplesBox--chartSixLight',
          name: 'var(--tui-color-chart-transparent-6);',
        },
        {
          class: 'tui-colourSamplesBox--chartSeven',
          name: 'var(--tui-color-chart-background-7);',
        },
        {
          class: 'tui-colourSamplesBox--chartSevenLight',
          name: 'var(--tui-color-chart-transparent-7);',
        },
      ],
      codeTemplate: `<div class="tui-example__box">Hello</div>`,
      codeScript: `<style lang="scss">
  .tui-example__box {
    background: var(--tui-color-primary);
  }
</style>`,
    };
  },
};
</script>

<style lang="scss">
.tui-colourSamplesBox {
  width: 30px;
  height: 30px;
}

.tui-colourSamplesBox--primary {
  background: var(--tui-color-primary);
}

.tui-colourSamplesBox--secondary {
  background: var(--tui-color-secondary);
}

.tui-colourSamplesBox--tertiary {
  background: var(--tui-color-tertiary);
}

.tui-colourSamplesBox--state {
  background: var(--tui-color-state);
}

.tui-colourSamplesBox--stateHover {
  background: var(--tui-color-state-hover);
}

.tui-colourSamplesBox--stateFocus {
  background: var(--tui-color-state-focus);
}

.tui-colourSamplesBox--stateActive {
  background: var(--tui-color-state-active);
}

.tui-colourSamplesBox--stateHighlight {
  background: var(--tui-color-state-highlight);
}

.tui-colourSamplesBox--stateDisabled {
  background: var(--tui-color-state-disabled);
}

.tui-colourSamplesBox--promptInfo {
  background: var(--tui-color-prompt-info);
}

.tui-colourSamplesBox--promptInfoBg {
  background: var(--tui-color-prompt-info-bg);
}

.tui-colourSamplesBox--promptAlert {
  background: var(--tui-color-prompt-alert);
}

.tui-colourSamplesBox--promptAlertBg {
  background: var(--tui-color-prompt-alert-bg);
}

.tui-colourSamplesBox--promptWarning {
  background: var(--tui-color-prompt-warning);
}

.tui-colourSamplesBox--promptWarningBg {
  background: var(--tui-color-prompt-warning-bg);
}

.tui-colourSamplesBox--promptSuccess {
  background: var(--tui-color-prompt-success);
}

.tui-colourSamplesBox--promptSuccessBg {
  background: var(--tui-color-prompt-success-bg);
}

.tui-colourSamplesBox--neutralOne {
  background: var(--tui-color-neutral-1);
}

.tui-colourSamplesBox--neutralTwo {
  background: var(--tui-color-neutral-2);
}

.tui-colourSamplesBox--neutralThree {
  background: var(--tui-color-neutral-3);
}

.tui-colourSamplesBox--neutralFour {
  background: var(--tui-color-neutral-4);
}

.tui-colourSamplesBox--neutralFive {
  background: var(--tui-color-neutral-5);
}

.tui-colourSamplesBox--neutralSix {
  background: var(--tui-color-neutral-6);
}

.tui-colourSamplesBox--neutralSeven {
  background: var(--tui-color-neutral-7);
}

.tui-colourSamplesBox--chartOne {
  background: var(--tui-color-chart-background-1);
}

.tui-colourSamplesBox--chartOneLight {
  background: var(--tui-color-chart-transparent-1);
}

.tui-colourSamplesBox--chartTwo {
  background: var(--tui-color-chart-background-2);
}

.tui-colourSamplesBox--chartTwoLight {
  background: var(--tui-color-chart-transparent-2);
}

.tui-colourSamplesBox--chartThree {
  background: var(--tui-color-chart-background-3);
}

.tui-colourSamplesBox--chartThreeLight {
  background: var(--tui-color-chart-transparent-3);
}

.tui-colourSamplesBox--chartFour {
  background: var(--tui-color-chart-background-4);
}

.tui-colourSamplesBox--chartFourLight {
  background: var(--tui-color-chart-transparent-4);
}

.tui-colourSamplesBox--chartFive {
  background: var(--tui-color-chart-background-5);
}

.tui-colourSamplesBox--chartFiveLight {
  background: var(--tui-color-chart-transparent-5);
}

.tui-colourSamplesBox--chartSix {
  background: var(--tui-color-chart-background-6);
}

.tui-colourSamplesBox--chartSixLight {
  background: var(--tui-color-chart-transparent-6);
}

.tui-colourSamplesBox--chartSeven {
  background: var(--tui-color-chart-background-7);
}

.tui-colourSamplesBox--chartSevenLight {
  background: var(--tui-color-chart-transparent-7);
}
</style>
