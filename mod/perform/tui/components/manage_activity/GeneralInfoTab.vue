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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package totara_perform
-->

<template>
  <Form class="tui-performManageActivityGeneralInfo">
    <FormRow
      v-slot="{ id, label }"
      :label="$str('general_info_label_activity_title', 'mod_perform')"
    >
      <InputText
        :id="id"
        :value="activity.edit_name"
        :placeholder="label"
        @input="updateActivity({ edit_name: $event })"
      />
    </FormRow>

    <FormRow
      v-slot="{ id, label }"
      :label="$str('general_info_label_activity_description', 'mod_perform')"
    >
      <Textarea
        :id="id"
        :value="activity.edit_description"
        :placeholder="label"
        @input="updateActivity({ edit_description: $event })"
      />
    </FormRow>

    <FormRow
      v-slot="{ id, label }"
      :label="$str('general_info_label_activity_type', 'mod_perform')"
    >
      <SelectFilter
        v-if="isNew"
        v-model="activityTypeSelection"
        :label="$str('general_info_label_activity_type', 'mod_perform')"
        :options="activityTypes"
        :show-label="false"
        :stacked="false"
      />
      <span v-else>{{ activityTypeName }}</span>
    </FormRow>

    <FormRow :style="actionButtonStyling">
      <ButtonGroup>
        <Button
          :styleclass="{ primary: true }"
          :text="submitButtonText"
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
import InputText from 'totara_core/components/form/InputText';
import SelectFilter from 'totara_core/components/filters/SelectFilter';
import Textarea from 'totara_core/components/form/Textarea';
import UpdateGeneralInfoMutation from 'mod_perform/graphql/update_activity_general_info.graphql';
import CreateActivityMutation from 'mod_perform/graphql/create_activity.graphql';

//GraphQL
import ActivityTypesQuery from 'mod_perform/graphql/activity_types';

export default {
  components: {
    Button,
    ButtonGroup,
    Form,
    FormRow,
    InputText,
    SelectFilter,
    Textarea,
  },
  props: {
    value: {
      type: Object,
      required: false,
    },
    disableAfterSave: {
      type: Boolean,
      required: false,
      default: false,
    },
    useModalStyling: {
      type: Boolean,
      required: false,
      default: false,
    },
    submitButtonText: {
      type: String,
      required: false,
      default() {
        return this.$str('save_changes', 'mod_perform');
      },
    },
  },
  data() {
    const activity = Object.assign({}, this.value);
    const typeId = activity && activity.id ? activity.type.id : 0;
    const typeName =
      activity && activity.id ? activity.type.display_name : 'unknown';

    return {
      activityTypes: [
        {
          id: 0,
          label: this.$str('general_info_select_activity_type', 'mod_perform'),
        },
      ],
      activityTypeSelection: typeId,
      activityTypeName: typeName,
      isSaving: false,
      mutationError: null,
      activity: activity,
    };
  },
  computed: {
    /**
     * Has the activity been not yet been saved to the back-end.
     *
     * @returns {boolean}
     */
    exists() {
      return Boolean(this.activity.id);
    },

    /**
     * Has the activity been saved to the back-end.
     *
     * @returns {boolean}
     */
    isNew() {
      return !this.activity.id;
    },

    /**
     * Is the title/name text empty.
     *
     * @returns {boolean}
     */
    hasNoTitle() {
      return (
        !this.activity.edit_name || this.activity.edit_name.trim().length === 0
      );
    },

    /**
     * Is the activity type unselected.
     *
     * @returns {boolean}
     */
    hasNoType() {
      return this.activityTypeSelection === 0;
    },

    /**
     * This is a hack to make the action button look okish in the create modal context.
     */
    actionButtonStyling() {
      if (this.useModalStyling) {
        return { marginTop: 'var(--tui-gap-6)', marginBottom: '0' };
      }

      return null;
    },
  },
  methods: {
    /**
     * Emmit an input event with an updated activity object, changes are patched into the existing value (activity).
     *
     * @param {object} update - The new values to patch into the activity object emitted.
     */
    updateActivity(update) {
      this.activity = Object.assign({}, this.activity, update);

      this.$emit('input', this.activity);
    },

    /**
     * Try to persist the activity to the back end.
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave() {
      this.isSaving = true;
      this.activityTypeName = this.activityTypes[
        this.activityTypeSelection
      ].label;

      try {
        const savedActivity = await this.save();
        this.updateActivity(savedActivity);
        this.$emit('mutation-success', savedActivity);

        // On create (from the modal) we keep the submit button disabled while the redirect is processing.
        if (!this.disableAfterSave) {
          this.isSaving = false;
        }
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
      let mutation, mutationName, variables;

      if (this.exists) {
        mutation = UpdateGeneralInfoMutation;
        mutationName = 'mod_perform_update_activity_general_info';

        variables = {
          activity_id: this.activity.id,
          name: this.activity.edit_name,
          description: this.activity.edit_description,
        };
      } else {
        mutation = CreateActivityMutation;
        mutationName = 'mod_perform_create_activity';

        variables = {
          name: this.activity.edit_name,
          description: this.activity.edit_description,
          type: this.activityTypeSelection,
        };
      }

      const { data: resultData } = await this.$apollo.mutate({
        mutation,
        variables,
        refetchAll: false, // Don't refetch all the data again
      });

      return resultData[mutationName].activity;
    },
  },

  apollo: {
    activityTypes: {
      query: ActivityTypesQuery,
      variables() {
        return [];
      },
      update({ mod_perform_activity_types: types }) {
        const options = types
          .map(type => {
            return { id: type.id, label: type.display_name };
          })
          .sort((a, b) => a.label.localeCompare(b.label));

        options.unshift({
          id: 0,
          label: this.$str('general_info_select_activity_type', 'mod_perform'),
        });

        return options;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "general_info_label_activity_description",
      "general_info_label_activity_title",
      "general_info_label_activity_type",
      "general_info_select_activity_type",
      "save_changes"
    ]
  }
</lang-strings>
