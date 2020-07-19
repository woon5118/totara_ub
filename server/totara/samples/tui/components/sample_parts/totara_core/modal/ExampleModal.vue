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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_samples
-->

<template>
  <Modal size="normal" :aria-labelledby="$id('title')">
    <ModalContent
      :title="name ? 'Hello ' + name : 'What is your name?'"
      :title-id="$id('title')"
      :close-button="true"
    >
      <p>Content</p>
      <p><InputText v-model="name" placeholder="John Smith" /></p>
      <p><InputText v-model="name" placeholder="John Smith" /></p>

      <div>{{ message }}</div>

      <template v-slot:buttons>
        <OkCancelGroup @ok="confirm" @cancel="close" />
      </template>
    </ModalContent>
  </Modal>
</template>

<script>
import InputText from 'totara_core/components/form/InputText';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import OkCancelGroup from 'totara_core/components/buttons/OkCancelGroup';

export default {
  components: {
    InputText,
    Modal,
    ModalContent,
    OkCancelGroup,
  },

  props: {
    message: String,
  },

  data: function() {
    return {
      name: '',
    };
  },

  methods: {
    close() {
      this.$emit('request-close');
    },

    confirm() {
      this.$emit('request-close', { result: { name: this.name } });
    },
  },
};
</script>
