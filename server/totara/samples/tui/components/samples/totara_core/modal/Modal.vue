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
    A standard modal used for displaying an important message or action to the
    user.

    <SamplesExample>
      <Button text="Show modal" @click="showModal" />

      <ModalPresenter :open="modalOpen" @request-close="modalResponse">
        <Modal :size="size" :aria-labelledby="$id('title')">
          <ModalContent
            :title="title"
            :title-id="$id('title')"
            :title-visible="isVisible"
            :close-button="size !== 'sheet'"
          >
            <br />
            <FormRow v-slot="{ id, label }" label="Name">
              <InputText :id="id" v-model="name" :placeholder="label" />
            </FormRow>

            <template v-slot:buttons>
              <OkCancelGroup @ok="confirm" @cancel="close" />
            </template>
          </ModalContent>
        </Modal>
      </ModalPresenter>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id, label }" label="Title">
        <InputText :id="id" v-model="title" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{}" label="Size">
        <RadioGroup v-model="size" :horizontal="true">
          <Radio value="small">small</Radio>
          <Radio value="normal">normal</Radio>
          <Radio value="large">large</Radio>
          <Radio value="sheet">sheet</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{}" label="Title visible">
        <Checkbox v-model="isVisible" />
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
import InputText from 'totara_core/components/form/InputText';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import OkCancelGroup from 'totara_core/components/buttons/OkCancelGroup';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Checkbox from 'totara_core/components/form/Checkbox';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Button,
    FormRow,
    InputText,
    Modal,
    ModalContent,
    ModalPresenter,
    OkCancelGroup,
    Radio,
    RadioGroup,
    Checkbox,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      modalOpen: false,
      name: '',
      size: 'normal',
      title: 'Enter your name',
      isVisible: true,
      codeTemplate: `<ModalPresenter :open="modalOpen" @request-close="modalResponse">
  <Modal :size="size" :aria-labelledby="$id('title')">
    <ModalContent
      :title="title"
      :title-id="$id('title')"
      :title-visible="isVisible"
      :close-button="true"
    >
      Some text...

      <template v-slot:buttons>
        <OkCancelGroup @ok="confirm" @cancel="close" />
      </template>
    </ModalContent>
  </Modal>
</ModalPresenter>`,
      codeScript: `import Modal from 'totara_core/components/modal/Modal;
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import OkCancelGroup from 'totara_core/components/buttons/OkCancelGroup';

export default {
  components: {
    Modal,
    ModalContent,
    ModalPresenter,
    OkCancelGroup,
  }
}

methods: {
  showModal() {
    this.modalOpen = true;
  },

  modalResponse(e) {
    if (e.result) {
      console.log(e.result);
    }
    this.modalOpen = false;
  },

  close() {
    this.modalResponse('');
  },

  confirm() {
    this.modalResponse({ result: { name: this.name } });
  },
},`,
    };
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    modalResponse(e) {
      if (e.result) {
        console.log(e.result);
      }
      this.modalOpen = false;
    },

    close() {
      this.modalResponse('');
    },

    confirm() {
      this.modalResponse({ result: { name: this.name } });
    },
  },
};
</script>
