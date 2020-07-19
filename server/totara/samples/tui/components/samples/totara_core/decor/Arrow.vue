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
  @package totara_samples
-->

<template>
  <div class="tui-testArrow">
    The arrow component is a visual component used with overlay or expanded
    content.

    <SamplesExample>
      <div class="tui-testArrow--box">
        <Arrow :size="size" :side="side" :distance="distance" />
      </div>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{}" label="Arrow side">
        <RadioGroup v-model="side" :horizontal="true">
          <Radio value="top">Top</Radio>
          <Radio value="right">Right</Radio>
          <Radio value="bottom">Bottom</Radio>
          <Radio value="left">Left</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{}" label="Arrow size">
        <RadioGroup v-model="size" :horizontal="true">
          <Radio value="normal">Normal</Radio>
          <Radio value="large">Large</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{}" label="Distance (offset)">
        <RadioGroup v-model="distance" :horizontal="true">
          <Radio value="16">16</Radio>
          <Radio value="32">32</Radio>
          <Radio value="64">64</Radio>
        </RadioGroup>
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Arrow from 'totara_core/components/decor/Arrow';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Arrow,
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      distance: '32',
      side: 'top',
      size: 'normal',
      codeTemplate: `<Arrow :size="size" :side="side" :distance="distance" />`,
      codeScript: `import Arrow from 'totara_core/components/decor/Arrow';

export default {
  components: {
    Arrow,
  }
}`,
    };
  },
};
</script>

<style lang="scss">
.tui-testArrow {
  &--box {
    position: relative;
    width: 50%;
    height: 100px;
    background: var(--tui-color-neutral-1);
    border: 1px solid var(--tui-color-neutral-5);
  }
}
</style>
