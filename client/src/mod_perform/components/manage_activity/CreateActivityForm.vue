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
  <Form>
    <FormRow
      v-slot="{ id }"
      :label="$str('general_info_label_activity_title', 'mod_perform')"
      required
    >
      <InputText
        :id="id"
        v-model="form.name"
        :maxlength="ACTIVITY_NAME_MAX_LENGTH"
      />
    </FormRow>

    <FormRow
      v-slot="{ id }"
      :label="$str('general_info_label_activity_description', 'mod_perform')"
    >
      <Textarea :id="id" v-model="form.description" />
    </FormRow>

    <FormRow
      v-slot="{ id }"
      :label="$str('general_info_label_activity_type', 'mod_perform')"
      :required="true"
    >
      <div>
        <Select
          :id="id"
          v-model="form.type"
          :aria-labelledby="id"
          :aria-describedby="$id('aria-describedby')"
          :options="activityTypes"
        />
        <FormRowDetails :id="$id('aria-describedby')">
          {{ $str('activity_type_help_text', 'mod_perform') }}
        </FormRowDetails>
      </div>
    </FormRow>

    <FormRow>
      <ButtonGroup>
        <Button
          :styleclass="{ primary: true }"
          :text="$str('get_started', 'mod_perform')"
          :disabled="isSaving || hasNoTitle || hasNoType"
          type="submit"
          @click.prevent="trySave"
        />
      </ButtonGroup>
    </FormRow>
  </Form>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import FormRowDetails from 'totara_core/components/form/FormRowDetails';
import InputText from 'totara_core/components/form/InputText';
import Select from 'totara_core/components/form/Select';
import Textarea from 'totara_core/components/form/Textarea';
import { ACTIVITY_NAME_MAX_LENGTH } from 'mod_perform/constants';

//GraphQL
import activityTypesQuery from 'mod_perform/graphql/activity_types';
import createActivityMutation from 'mod_perform/graphql/create_activity';

export default {
  components: {
    Button,
    ButtonGroup,
    Form,
    FormRow,
    FormRowDetails,
    InputText,
    Select,
    Textarea,
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
          label: this.$str('general_info_select_activity_type', 'mod_perform'),
        },
      ],
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

  apollo: {
    activityTypes: {
      query: activityTypesQuery,
      variables() {
        return [];
      },
      update({ mod_perform_activity_types: types }) {
        const options = types
          .map(type => {
            return { id: type.id, label: type.display_name };
          })
          .sort((a, b) => a.label.localeCompare(b.label));

        //show 'select type'
        options.unshift({
          id: 0,
          label: this.$str('general_info_select_activity_type', 'mod_perform'),
        });
        return options;
      },
    },
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
      "general_info_label_activity_description",
      "general_info_label_activity_title",
      "general_info_label_activity_type",
      "general_info_select_activity_type",
      "get_started"
    ]
  }
</lang-strings>
