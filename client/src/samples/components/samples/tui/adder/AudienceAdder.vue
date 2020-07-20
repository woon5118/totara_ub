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
  @module samples
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
import AudienceAdder from 'tui/components/adder/AudienceAdder';

import Button from 'tui/components/buttons/Button';
import SamplesCode from 'samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'samples/components/sample_parts/misc/SamplesExample';

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
      codeScript: `import AudienceAdder from 'tui/components/adder/AudienceAdder';

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
