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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module samples
-->

<template>
  <div>
    <ButtonLabel
      number="444444"
      aria-label="aria-Label-Sample1"
      @open="showModal"
      @click="buttonClicked"
    >
      <template v-slot:icon>
        <Add />
      </template>
      <template v-slot:hoverContent>
        <div>
          <span>1234</span>
          <span>5678</span>
        </div>
      </template>
    </ButtonLabel>
    <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
      <ExampleModal message="Hello" />
    </ModalPresenter>

    <ButtonLabel
      number="1"
      aria-label="aria-Label-Sample2"
      @open="showSheetModal"
      @click="buttonClicked"
    >
      <template v-slot:icon>
        <AddUser />
      </template>
      <template v-slot:hoverContent>
        <div>
          <p>12345678</p>
          <p>abcdefghi</p>
        </div>
      </template>
    </ButtonLabel>
    <ModalPresenter
      :open="sheetModalOpen"
      @request-close="sheetModalRequestClose"
    >
      <ExampleModalSheet />
    </ModalPresenter>

    <ButtonLabel
      number="1000"
      aria-label="aria-Label-Sample3"
      @click="buttonClicked"
    >
      <template v-slot:icon>
        <Add />
      </template>
    </ButtonLabel>
  </div>
</template>

<script>
import ButtonLabel from 'totara_engage/components/buttons/ButtonLabel';
import Add from 'tui/components/icons/common/Add';
import AddUser from 'tui/components/icons/common/AddUser';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import ExampleModal from 'samples/components/sample_parts/tui/modal/ExampleModal';
import ExampleModalSheet from 'samples/components/sample_parts/tui/modal/ExampleModalSheet';

export default {
  components: {
    ButtonLabel,
    Add,
    AddUser,
    ModalPresenter,
    ExampleModal,
    ExampleModalSheet,
  },

  data() {
    return {
      modalOpen: false,
      sheetModalOpen: false,
    };
  },

  methods: {
    showModal() {
      this.modalOpen = true;
    },

    modalRequestClose(e) {
      console.log(e.result);
      this.modalOpen = false;
    },

    showSheetModal() {
      this.sheetModalOpen = true;
    },

    sheetModalRequestClose(e) {
      console.log(e.result);
      this.sheetModalOpen = false;
    },

    buttonClicked() {
      console.log('button clicked');
    },
  },
};
</script>
