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
  <div class="tui-loader">
    A visual UI component

    <SamplesExample>
      <Button :text="$str('add_audiences', 'totara_core')" @click="adderOpen" />

      <AudienceAdder
        :open="showAdder"
        :custom-query="query"
        :existing-items="addedIds"
        @added="adderUpdate"
        @cancel="adderCancelled"
      />

      <h5>Selected Items:</h5>
      <div v-for="audience in addedAudiences" :key="audience.id">
        {{ audience }}
      </div>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import AudienceAdder from 'totara_core/components/adder/AudienceAdder';

import Button from 'totara_core/components/buttons/Button';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';

// Queries
import cohorts from 'core/graphql/cohorts';

export default {
  components: {
    AudienceAdder,
    Button,
    SamplesCode,
    SamplesExample,
  },

  data() {
    return {
      addedAudiences: [],
      addedIds: [],
      showAdder: false,
      query: cohorts,
      codeTemplate: `<Button :text="$str('add_audiences', 'totara_core')" @click="adderOpen" />

<AudienceAdder
  :open="showAdder"
  :existing-items="addedIds"
  @added="adderUpdate"
  @cancel="adderCancelled"
/>

<h5>Selected Items:</h5>
<div v-for="audience in addedAudiences" :key="audience.id">
  {{ audience }}
</div>
`,
      codeScript: `import AudienceAdder from 'totara_core/components/adder/AudienceAdder';

export default {
  components: {
    AudienceAdder,
  },

  data() {
    return {
      addedAudiences: [],
      addedIds: [],
      showAdder: false,
    }
  },

  methods: {
    adderOpen() {
      this.showAdder = true;
    },

    adderCancelled() {
      this.showAdder = false;
    },

    adderUpdate(selection) {
      this.addedIds = selection.ids;
      this.addedAudiences = selection.data;
      this.showAdder = false;
    },
  },
}`,
    };
  },

  methods: {
    adderOpen() {
      this.showAdder = true;
    },

    adderCancelled() {
      this.showAdder = false;
    },

    adderUpdate(selection) {
      this.addedIds = selection.ids;
      this.addedAudiences = selection.data;
      this.showAdder = false;
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "add_audiences"
  ]
}
</lang-strings>
