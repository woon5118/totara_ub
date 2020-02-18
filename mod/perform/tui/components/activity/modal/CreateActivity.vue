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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->

<template>
  <Modal size="large" :aria-labelledby="$id('title')">
    <ModalContent
      :title="
        $str('perform:add_activity', 'mod_perform') +
          ' : ' +
          $str('perform:create_activity:general_settings', 'mod_perform')
      "
      :title-id="$id('title')"
      :close-button="true"
    >
      <Form @submit.prevent="createActivity">
        <FormRow
          v-slot="{ id }"
          :label="$str('perform:create_activity:name', 'mod_perform')"
        >
          <InputText :id="id" v-model="name" :required="true" />
        </FormRow>
        <FormRow
          v-slot="{ id }"
          :label="$str('perform:create_activity:description', 'mod_perform')"
        >
          <Textarea :id="id" v-model="description" :rows="4" />
        </FormRow>

        <SubmitButton
          :text="$str('perform:create_activity:save', 'mod_perform')"
        />
      </Form>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import SubmitButton from 'totara_core/components/buttons/Submit';
import Textarea from 'totara_core/components/form/Textarea';
import CreateActivityMutation from '../../../../webapi/ajax/create_activity.graphql';

export default {
  components: {
    Modal,
    ModalContent,
    Form,
    FormRow,
    InputText,
    SubmitButton,
    Textarea,
  },

  data: function() {
    return {
      name: '',
      description: '',
    };
  },

  methods: {
    createActivity() {
      this.$apollo
        .mutate({
          // Query
          mutation: CreateActivityMutation,
          // Parameters
          variables: {
            name: this.name,
            description: this.description,
          },
        })
        .then(data => {
          if (data.data && data.data.mod_perform_create_activity) {
            this.$emit('request-close');
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "perform:add_activity",
      "perform:create_activity:general_settings",
      "perform:create_activity:name",
      "perform:create_activity:description",
      "perform:create_activity:save"
    ]
  }
</lang-strings>
