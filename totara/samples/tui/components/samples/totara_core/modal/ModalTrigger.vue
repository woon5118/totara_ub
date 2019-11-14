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
    A modal trigger for calling a stand-alone modal component.

    <SamplesExample>
      <Button text="Show modal" @click="showModal" />
      <ModalPresenter :open="modalOpen" @request-close="modalResponse">
        <ExampleModal message="Hello" />
      </ModalPresenter>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ExampleModal from 'totara_samples/components/sample_parts/totara_core/modal/ExampleModal';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';

export default {
  components: {
    Button,
    ExampleModal,
    ModalPresenter,
    SamplesCode,
    SamplesExample,
  },

  data() {
    return {
      message: 'Are you sure you want to do this?',
      modalOpen: false,
      name: '',
      title: 'This is a simple confirmation modal.',
      codeTemplate: `<ModalPresenter :open="modalOpen" @request-close="modalResponse">
  <ExampleModal/>
</ModalPresenter>`,
      codeScript: `import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import CustomModal from '../CustomModal';

export default {
  components: {
    ConfirmationModal,
    CustomModal,
  }
}

methods: {
  showModal() {
    this.modalOpen = true;
  },

  modalResponse(e) {
    console.log(e.result);
    this.modalOpen = false;
  },
},`,
    };
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    modalResponse(e) {
      console.log(e.result);
      this.modalOpen = false;
    },
  },
};
</script>
