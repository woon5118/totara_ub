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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package totara_perform
-->

<template>
  <Modal :aria-labelledby="$id('title')">
    <ModalContent
      :title="$str('add_activity', 'mod_perform')"
      :title-id="$id('title')"
      :close-button="false"
      @dismiss="$emit('cancel')"
    >
      <Form>
        <FormRow
          v-slot="{ id }"
          :label="$str('create_activity_title', 'mod_perform')"
          required
        >
          <InputText
            :id="id"
            v-model="form.name"
            :maxlength="ACTIVITY_NAME_MAX_LENGTH"
            char-length="full"
          />
        </FormRow>

        <FormRow
          v-slot="{ id }"
          :label="
            $str('general_info_label_activity_description', 'mod_perform')
          "
        >
          <Textarea :id="id" v-model="form.description" char-length="full" />
          <FormRowDetails :id="$id('aria-describedby')">
            {{ $str('you_can_add_this_later', 'mod_perform') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          v-slot="{ id }"
          :label="$str('create_activity_type', 'mod_perform')"
          :required="true"
        >
          <div>
            <Select
              :id="id"
              v-model="form.type"
              :aria-labelledby="id"
              :aria-describedby="$id('aria-describedby')"
              :options="activityTypes"
              char-length="10"
            />
            <FormRowDetails :id="$id('aria-describedby')">
              {{ $str('activity_type_help_text', 'mod_perform') }}
            </FormRowDetails>
          </div>
        </FormRow>
      </Form>

      <template v-slot:buttons>
        <Button
          :styleclass="{ primary: true }"
          :text="$str('button_create', 'mod_perform')"
          :disabled="isSaving || hasNoTitle || hasNoType"
          type="submit"
          @click.prevent="trySave"
        />

        <CancelButton :disabled="isSaving" @click="$emit('request-close')" />
      </template>
    </ModalContent>
  </Modal>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import CancelButton from 'tui/components/buttons/Cancel';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import FormRowDetails from 'tui/components/form/FormRowDetails';
import InputText from 'tui/components/form/InputText';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import Select from 'tui/components/form/Select';
import Textarea from 'tui/components/form/Textarea';
import { ACTIVITY_NAME_MAX_LENGTH } from 'mod_perform/constants';

//GraphQL
import createActivityMutation from 'mod_perform/graphql/create_activity';

export default {
  components: {
    Button,
    CancelButton,
    Form,
    FormRow,
    FormRowDetails,
    InputText,
    Modal,
    ModalContent,
    Select,
    Textarea,
  },

  props: {
    types: Array,
  },

  data() {
    return {
      form: {
        name: '',
        description: '',
        type: 0,
      },
      activityTypes: [
        {
          id: 0,
          label: this.$str('create_activity_select_placeholder', 'mod_perform'),
        },
      ].concat(this.types),
      isSaving: false,
      mutationError: null,
    };
  },

  computed: {
    /**
     * Is the title/name text empty.
     *
     * @return {boolean}
     */
    hasNoTitle() {
      return !this.form.name || this.form.name.trim().length === 0;
    },

    /**
     * Is the activity type unselected.
     *
     * @return {boolean}
     */
    hasNoType() {
      return this.form.type === 0;
    },
  },

  created() {
    this.ACTIVITY_NAME_MAX_LENGTH = ACTIVITY_NAME_MAX_LENGTH;
  },

  methods: {
    /**
     * Try to persist the activity to the back end.
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave() {
      this.isSaving = true;

      try {
        const savedActivity = await this.save();
        this.$emit('mutation-success', savedActivity);
      } catch (e) {
        this.$emit('mutation-error', e);

        // If something goes wrong during create, allow the user to try again.
        this.isSaving = false;
      }
    },

    /**
     * @returns {Promise<{id, name, description}>}
     */
    async save() {
      const { data: resultData } = await this.$apollo.mutate({
        mutation: createActivityMutation,
        variables: this.form,
        refetchAll: false,
      });

      return resultData.mod_perform_create_activity.activity;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_type_help_text",
      "add_activity",
      "button_create",
      "create_activity_select_placeholder",
      "create_activity_title",
      "create_activity_type",
      "general_info_label_activity_description",
      "you_can_add_this_later"
    ]
  }
</lang-strings>
