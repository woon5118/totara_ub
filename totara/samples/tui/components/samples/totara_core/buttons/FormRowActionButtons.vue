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
  <div class="tui-testButton">
    A helper component for quickly adding submit / cancel buttons to a form.

    <SamplesExample>
      <form>
        <FormRowActionButtons
          @cancel="formCancel"
          @submit.prevent="formSubmit"
        />
      </form>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
// Components
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';

export default {
  components: {
    FormRowActionButtons,
    SamplesCode,
    SamplesExample,
  },

  data() {
    return {
      disabled: false,
      primaryStyle: false,
      smallStyle: false,
      screenReaderStyle: false,
      text: 'Button',
      transparentStyle: false,
      codeTemplate: `<FormRowActionButtons
  @cancel="formCancel"
  @submit.prevent="formSubmit"
/>`,
      codeScript: `import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';

export default {
  components: {
    FormRowActionButtons,
  }
}`,
    };
  },

  methods: {
    formCancel() {
      this.enableFormInputs();
    },

    formSubmit() {
      this.disableFormInputs();
    },
  },
};
</script>

<style lang="scss">
.tui-testButton {
  h4 {
    margin-top: var(--tui-gap-6);
  }
}
</style>
