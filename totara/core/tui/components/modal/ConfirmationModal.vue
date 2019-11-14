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
  @package totara_core
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
