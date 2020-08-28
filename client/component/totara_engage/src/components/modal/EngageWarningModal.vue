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

  @author Qingyang Liu <Qingyang liu@totaralearning.com>
  @module totara_engage
-->
<template>
  <Modal
    :dismissable="dismissable"
    :aria-labelledby="id"
    class="tui-engageWarningModal"
  >
    <ModalContent
      :close-button="closeButton"
      :title-id="id"
      @dismiss="$emit('request-close')"
    >
      <div class="tui-engageWarningModal__container">
        <div class="tui-engageWarningModal__icon">
          <warning size="700" :custom-class="'tui-icon--warning'" />
        </div>
        <div class="tui-engageWarningModal__box">
          <p v-show="title" class="tui-engageWarningModal__title">
            {{ title }}
          </p>

          <p>
            {{ messageContent }}
          </p>
        </div>
      </div>
      <template v-slot:buttons>
        <ButtonGroup>
          <Button
            :styleclass="{ primary: true, small: true }"
            :text="$str('yes', 'moodle')"
            @click="$emit('delete')"
          />

          <Button
            :styleclass="{ small: true }"
            :text="$str('no', 'moodle')"
            @click="$emit('request-close')"
          />
        </ButtonGroup>
      </template>
    </ModalContent>
  </Modal>
</template>
<script>
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import Warning from 'tui/components/icons/Warning';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Button from 'tui/components/buttons/Button';

export default {
  components: {
    Modal,
    ModalContent,
    Warning,
    ButtonGroup,
    Button,
  },

  props: {
    closeButton: {
      type: Boolean,
      default: true,
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
      default: '',
    },

    messageContent: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      dismissable: {
        overlayClose: false,
        esc: true,
        backdropClick: false,
      },
      id: this.$id(this.title),
    };
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "yes",
      "no"
    ]
  }
</lang-strings>
