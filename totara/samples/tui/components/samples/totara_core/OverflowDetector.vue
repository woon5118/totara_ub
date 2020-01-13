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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div>
    The overflow detector component is used to detect which items fit in a
    container, so the ones that don't fit can be shown in a dropdown menu or
    similar.
    <SamplesExample>
      <div class="tui-sampleOverflow">
        <p><Button text="Randomize order" @click="randomize" /></p>
        <p>Main container ({{ visible }} visible):</p>
        <p>
          <OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
            <div class="tui-sampleOverflow__items">
              <Button
                v-for="(text, i) in items"
                v-show="measuring || i < visible"
                :key="i"
                class="tui-sampleOverflow__item"
                :text="text"
              />
            </div>
          </OverflowDetector>
        </p>
        <p>Overflowing: {{ overflowing ? 'Yes' : 'No' }}</p>
        <p>Overflowed items:</p>
        <p>
          <Button
            v-for="(text, i) in items.slice(visible)"
            :key="i"
            :text="text"
          />
        </p>
      </div>
    </SamplesExample>
    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
      <template v-slot:style>{{ codeStyle }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import OverflowDetector from 'totara_core/components/util/OverflowDetector';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';

export default {
  components: {
    Button,
    OverflowDetector,
    SamplesCode,
    SamplesExample,
  },

  data() {
    return {
      items: [
        'Photon',
        'Gluon',
        'W/Z boson',
        'Higgs boson',
        'Electron',
        'Positron',
        'Muon',
        'Tau',
        'Neutrino',
      ],
      visible: Infinity,
      overflowing: false,
      codeTemplate: `<OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
  <div class="tui-sampleOverflow__items">
    <Button
      v-for="(text, i) in items"
      v-show="measuring || i < visible"
      :key="i"
      class="tui-sampleOverflow__item"
      :text="text"
    />
  </div>
</OverflowDetector>`,
      codeScript: `import OverflowDetector from 'totara_core/components/util/OverflowDetector;

export default {
  components: {
    OverflowDetector,
  },

  data() {
    return {
      visible: Infinity,
    }
  },

  methods: {
    overflowChanged({ visible }) {
      this.visible = visible;
    },
  },
}`,
      codeStyle: `.tui-sampleOverflow {
  &__items {
    display: flex;
  }

  &__item {
    flex-shrink: 0;
  }
}`,
    };
  },

  methods: {
    overflowChanged({ visible, overflowing }) {
      this.visible = visible;
      this.overflowing = overflowing;
    },

    randomize() {
      this.items = this.items.slice().sort(() => Math.random() - 0.5);
    },
  },
};
</script>

<style lang="scss">
.tui-sampleOverflow {
  &__items {
    display: flex;
  }

  &__item {
    flex-shrink: 0;
  }
}
</style>
