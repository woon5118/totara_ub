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
  <div>
    A confirmation modal, to be applied when the user has triggered an action
    which is unchangeable or highly visible.

    <SamplesExample>
      <Button text="Show confirmation modal" @click="showModal" />

      <ConfirmationModal
        :open="modalOpen"
        :title="title"
        :confirm-button-text="
          useCustomConfirmText ? customConfirmText : undefined
        "
        :close-button="closeButton"
        :loading="loading"
        @confirm="modalConfirmed"
        @cancel="modalCancelled"
      >
        {{ message }}
      </ConfirmationModal>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id, label }" label="Title">
        <InputText :id="id" v-model="title" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Message">
        <InputText :id="id" v-model="message" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Use custom confirm button text">
        <Checkbox
          :id="id"
          v-model="useCustomConfirmText"
          :placeholder="label"
        />
      </FormRow>

      <FormRow
        v-if="useCustomConfirmText"
        v-slot="{ id, label }"
        label="Confirm button text"
      >
        <InputText :id="id" v-model="customConfirmText" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Has close button">
        <Checkbox :id="id" v-model="closeButton" :placeholder="label" />
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
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import Checkbox from 'totara_core/components/form/Checkbox';

export default {
  components: {
    Checkbox,
    Button,
    ConfirmationModal,
    FormRow,
    InputText,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      message: 'Are you sure you want to do this?',
      modalOpen: false,
      title: 'This is a simple confirmation modal.',
      customConfirmText: 'Delete',
      useCustomConfirmText: false,
      closeButton: false,
      loading: false,
      confirmButtonText: '',
      codeScript: `import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';

export default {
  components: {
    ConfirmationModal,
  },

  data() {
    return {
      loading: false,
    }
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    hideModal() {
      this.modalOpen = false;
      this.loading = false;
    },

    async modalConfirmed() {
      console.log('User confirmed the modal action');
      this.loading = true;
      setTimeout(() => this.hideModal(), 2000);
    },

    modalCancelled() {
      console.log('Cancelled');
      this.hideModal();
    },
  },
};
`,
    };
  },
  computed: {
    codeTemplate() {
      return `<ConfirmationModal
  :open="modalOpen"
  :title="title" ${
    this.useCustomConfirmText
      ? `
  :confirm-button-text="$str('confirm_activity_delete', 'mod_perform')"`
      : ''
  }
  :close-button="closeButton"
  :loading="loading"
  @confirm="modalConfirmed"
  @cancel="modalCancelled"
>
  {{ message }}
</ConfirmationModal>`;
    },
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    hideModal() {
      this.modalOpen = false;
      this.loading = false;
    },

    modalConfirmed() {
      console.log('User confirmed the modal action');
      this.loading = true;
      setTimeout(() => this.hideModal(), 2000);
    },

    modalCancelled() {
      console.log('User cancelled the modal action');
      this.hideModal();
    },
  },
};
</script>
