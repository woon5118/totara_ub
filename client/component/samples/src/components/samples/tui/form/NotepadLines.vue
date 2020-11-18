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
  <div class="tui-testArrow">
    A series of simple notepad style lines for printing unfilled text inputs and
    areas.

    <SamplesExample>
      <NotepadLines :lines="Number(lines)" :char-length="charLength" />
    </SamplesExample>

    <SamplesPropCtl>
      <h4 class="tui-samplesCtl__optional">Optional</h4>

      <FormRow v-slot="{ id }" label="Lines">
        <InputNumber :id="id" v-model="lines" :min="0" />
      </FormRow>

      <FormRow v-slot="{ id }" label="Char length">
        <InputText :id="id" v-model="charLength" />
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import FormRow from 'tui/components/form/FormRow';
import InputText from 'tui/components/form/InputText';
import InputNumber from 'tui/components/form/InputNumber';
import NotepadLines from 'tui/components/form/NotepadLines';
import SamplesCode from 'samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    FormRow,
    InputText,
    InputNumber,
    NotepadLines,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      charLength: 20,
      lines: 6,
      codeTemplate: `<NotepadLines :lines="6" :char-length="20">`,
      codeScript: `import NotepadLines from 'tui/components/form/NotepadLines';

export default {
  components: {
    NotepadLines,
  }
}`,
    };
  },
};
</script>
