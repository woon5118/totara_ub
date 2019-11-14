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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    An information modal, which forces the user to read information that is not
    actionable.

    <SamplesExample>
      <Button text="Show information modal" @click="showModal" />

      <InformationModal :open="modalOpen" :title="title" @close="modalClosed">
        {{ message }}
      </InformationModal>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id, label }" label="Title">
        <InputText :id="id" v-model="title" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Message">
        <InputText :id="id" v-model="message" :placeholder="label" />
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import FormRow from 'totara_core/components/form/FormRow';
import InformationModal from 'totara_core/components/modal/InformationModal';
import InputText from 'totara_core/components/form/InputText';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Button,
    FormRow,
    InformationModal,
    InputText,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      message:
        'You can not do this action yet. You must complete more things before you can.',
      modalOpen: false,
      title: 'This is a simple information modal.',
      codeScript: `import InformationModal from 'totara_core/components/modal/InformationModal';

export default {
  components: {
    InformationModal,
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    hideModal() {
      this.modalOpen = false;
    },

    modalClosed() {
      console.log('Closed');
      this.hideModal();
    },
  },
};
`,
    };
  },
  computed: {
    codeTemplate() {
      return `<InformationModal
  :open="modalOpen"
  :title="title"
  @close="modalClosed"
>
  {{ message }}
</InformationModal>`;
    },
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    hideModal() {
      this.modalOpen = false;
    },

    modalClosed() {
      console.log('User closed the modal');
      this.hideModal();
    },
  },
};
</script>
