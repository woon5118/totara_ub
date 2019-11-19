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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package pathway_manual
-->

<template>
  <ModalPresenter :open="open">
    <Modal size="normal" :aria-labelledby="id">
      <ModalContent
        :title="title"
        :title-id="id"
        :close-button="true"
        @dismiss="$emit('cancel')"
      >
        <slot />
        <template v-slot:buttons>
          <OkCancelGroup @ok="$emit('confirm')" @cancel="$emit('cancel')" />
        </template>
      </ModalContent>
    </Modal>
  </ModalPresenter>
</template>

<script>
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import OkCancelGroup from 'totara_core/components/buttons/OkCancelGroup';

export default {
  components: {
    Modal,
    ModalContent,
    ModalPresenter,
    OkCancelGroup,
  },

  props: {
    open: {
      required: true,
      type: Boolean,
    },
    title: {
      required: true,
      type: String,
    },
  },

  computed: {
    id() {
      return this.$id('title');
    },
  },
};
</script>
