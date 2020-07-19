<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module totara_core
-->

<template>
  <ModalPresenter :open="open" @request-close="$emit('cancel')">
    <Modal :size="size" :aria-labelledby="id">
      <ModalContent
        :close-button="closeButton"
        :title="title"
        :title-id="id"
        @dismiss="$emit('cancel')"
      >
        <slot />
        <template v-slot:buttons>
          <ButtonGroup>
            <Button
              :styleclass="{ primary: 'true' }"
              :disabled="loading"
              :text="confirmButtonText"
              @click="$emit('confirm')"
            />
            <ButtonCancel :disabled="loading" @click="$emit('cancel')" />
          </ButtonGroup>
        </template>
      </ModalContent>
    </Modal>
  </ModalPresenter>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    Modal,
    ModalContent,
    ModalPresenter,
  },

  props: {
    loading: {
      type: Boolean,
    },
    confirmButtonText: {
      type: String,
      default() {
        return this.$str('ok', 'moodle');
      },
    },
    closeButton: {
      type: Boolean,
      default: false,
    },
    open: {
      type: Boolean,
    },
    size: {
      type: String,
      default: 'normal',
    },
    title: {
      type: String,
    },
  },

  computed: {
    id() {
      return this.$id(this.title);
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "ok"
    ]
  }
</lang-strings>
