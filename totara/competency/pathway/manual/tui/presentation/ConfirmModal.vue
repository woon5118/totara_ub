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
  <Modal size="normal" :aria-labelledby="$id('title')">
    <ModalContent
      :title="$str('submit_ratings_confirmation_header', 'pathway_manual')"
      :title-id="$id('title')"
      :close-button="true"
    >
      <p>{{ getConfirmMsg() }}</p>
      <p>
        <strong>{{
          $str('submit_ratings_confirmation_question', 'pathway_manual')
        }}</strong>
      </p>

      <template v-slot:buttons>
        <OkCancelGroup @ok="confirm" @cancel="close" />
      </template>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'totara_core/presentation/modal/Modal';
import ModalContent from 'totara_core/presentation/modal/ModalContent';
import OkCancelGroup from 'totara_core/presentation/buttons/OkCancelGroup';

export default {
  components: {
    Modal,
    ModalContent,
    OkCancelGroup,
  },

  props: {
    numSelected: {
      required: true,
      type: Number,
    },
    isForSelf: {
      required: true,
      type: Boolean,
    },
    subjectUserFullname: {
      required: true,
      type: String,
    },
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
      this.$emit('confirm-submit');
    },
    getConfirmMsg() {
      let ratingSummary = 'submit_ratings_summary';
      ratingSummary += this.numSelected === 1 ? '_singular' : '_plural';
      ratingSummary += this.isForSelf ? '_self' : '_other';
      let confirmMsgParams = {
        amount: this.numSelected,
        subject_user: this.subjectUserFullname,
      };
      return this.$str(ratingSummary, 'pathway_manual', confirmMsgParams);
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "submit_ratings_confirmation_header",
      "submit_ratings_confirmation_question",
      "submit_ratings_summary_singular_self",
      "submit_ratings_summary_singular_other",
      "submit_ratings_summary_plural_self",
      "submit_ratings_summary_plural_other"
    ]
  }
</lang-strings>
