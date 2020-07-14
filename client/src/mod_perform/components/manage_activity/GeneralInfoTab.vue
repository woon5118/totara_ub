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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_perform
-->

<template>
  <div class="tui-performManageActivityGeneralInfo">
    <h3 class="tui-performManageActivityGeneralInfo__heading">
      {{ $str('activity_general_tab_heading', 'mod_perform') }}
    </h3>

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
      >
        <div>
          <span v-if="isActive" class="tui-performManageActivityGeneralInfo">{{
            value.type.display_name
          }}</span>
          <Select
            v-else
            :id="id"
            v-model="form.type_id"
            :aria-labelledby="id"
            :aria-describedby="$id('aria-describedby')"
            :options="activityTypes"
          />
        </div>
      </FormRow>

      <template>
        <h3 class="tui-performManageActivityGeneralInfo__heading">
          {{
            $str('activity_general_response_attribution_heading', 'mod_perform')
          }}
        </h3>

        <FormRow
          :label="
            $str('activity_general_anonymous_responses_label', 'mod_perform')
          "
        >
          <span v-if="isActive">{{
            activeToggleText(value.anonymous_responses)
          }}</span>
          <ToggleSwitch v-else v-model="form.anonymousResponse" toggle-first />
        </FormRow>
      </template>

      <div class="tui-performManageActivityManualRelationships">
        <h3 class="tui-performManageActivityManualRelationships__heading">
          {{
            $str('general_info_participant_selection_heading', 'mod_perform')
          }}
        </h3>
        <p>
          {{
            $str(
              'general_info_participant_selection_description',
              'mod_perform'
            )
          }}
        </p>
        <FormRow
          v-for="relationship in manualRelationships"
          :key="relationship.id"
          v-slot="{ id }"
          :label="relationship.name"
        >
          <div>
            <span v-if="isActive">
              {{ relationship.selector_relationship_name }}
            </span>
            <Select
              v-else
              :id="id"
              v-model="form.manualRelationshipSelections[relationship.id]"
              :aria-labelledby="id"
              :aria-label="relationship.name"
              :aria-describedby="$id('aria-describedby')"
              :options="manualRelationshipOptions"
            />
          </div>
        </FormRow>
      </div>

      <FormRow>
        <ButtonGroup>
          <Button
            :styleclass="{ primary: true }"
            :text="$str('save_changes', 'mod_perform')"
            :disabled="isSaving || hasNoTitle"
            type="submit"
            @click.prevent="trySave"
          />
          <Button
            :disabled="isSaving"
            :text="$str('cancel', 'moodle')"
            @click="resetChanges"
          />
        </ButtonGroup>
      </FormRow>
    </Form>
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import InputText from 'tui/components/form/InputText';
import Select from 'tui/components/form/Select';
import Textarea from 'tui/components/form/Textarea';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';
import {
  ACTIVITY_STATUS_ACTIVE,
  ACTIVITY_NAME_MAX_LENGTH,
} from 'mod_perform/constants';

//GraphQL
import activityTypesQuery from 'mod_perform/graphql/activity_types';
import manualRelationshipOptionsQuery from 'mod_perform/graphql/manual_relationship_selector_options';
import updateGeneralInfoMutation from 'mod_perform/graphql/update_activity';

export default {
  components: {
    Button,
    ButtonGroup,
    Form,
    FormRow,
    InputText,
    Select,
    Textarea,
    ToggleSwitch,
  },

  props: {
    value: {
      type: Object,
      required: true,
      validator(value) {
        let keys = Object.keys(value);

        return (
          keys.includes('edit_name') &&
          keys.includes('edit_description') &&
          keys.includes('anonymous_responses') &&
          keys.includes('manual_relationships')
        );
      },
    },
  },

  data() {
    return {
      form: {
        name: this.value.edit_name,
        description: this.value.edit_description,
        type_id: this.value.type.id,
        manualRelationshipSelections: this.getManualRelationshipSelections(),
        anonymousResponse: this.value && this.value.anonymous_responses,
      },
      activityTypes: [
        {
          id: this.value.type.id,
          label: this.value.type.display_name,
        },
      ],
      manualRelationshipOptions: this.value.manual_relationships.map(
        relationship => {
          return {
            id: relationship.selector_relationship.id,
            label: relationship.selector_relationship.name,
          };
        }
      ),
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
     * check activity status is active
     * @return {boolean}
     */
    isActive() {
      if (!this.value) {
        return false;
      }
      return this.value.state_details.name === ACTIVITY_STATUS_ACTIVE;
    },

    manualRelationships() {
      return this.value
        ? this.value.manual_relationships.map(relationship => {
            return {
              name: relationship.manual_relationship.name,
              id: relationship.manual_relationship.id,
              selector_relationship_id: relationship.selector_relationship.id,
              selector_relationship_name:
                relationship.selector_relationship.name,
            };
          })
        : [];
    },
  },

  created() {
    this.ACTIVITY_NAME_MAX_LENGTH = ACTIVITY_NAME_MAX_LENGTH;
  },

  mounted() {
    // Confirm navigation away if user is currently editing.
    window.addEventListener('beforeunload', this.unloadHandler);
  },

  apollo: {
    activityTypes: {
      query: activityTypesQuery,
      variables() {
        return [];
      },
      update({ mod_perform_activity_types: types }) {
        return types
          .map(type => {
            return { id: type.id, label: type.display_name };
          })
          .sort((a, b) => a.label.localeCompare(b.label));
      },
    },
    manualRelationshipOptions: {
      query: manualRelationshipOptionsQuery,
      variables() {
        return { activity_id: this.value.id };
      },
      update: ({
        mod_perform_manual_relationship_selector_options: relationshipOptions,
      }) => {
        return relationshipOptions.map(relationship => {
          return {
            id: relationship.id,
            label: relationship.name,
          };
        });
      },
    },
  },

  methods: {
    /**
     * Gets manual relationship selections as an object map. {manual_relationship_id: selected_relationship_id}
     *
     * @returns {Object}
     */
    getManualRelationshipSelections() {
      let relationshipSelections = {};
      if (this.value && this.value.manual_relationships) {
        this.value.manual_relationships.forEach(relationship => {
          relationshipSelections[relationship.manual_relationship.id] =
            relationship.selector_relationship.id;
        });
      }

      return relationshipSelections;
    },

    /**
     * Try to persist the activity to the back end.
     * Emitting events on success/failure.
     */
    async trySave() {
      this.isSaving = true;

      try {
        const savedActivity = await this.save();
        this.updateActivity(savedActivity);
        this.$emit('mutation-success', savedActivity);
      } catch (e) {
        this.$emit('mutation-error', e);
      }
      this.isSaving = false;
    },

    /**
     * @returns {Object}
     */
    async save() {
      let mutation = updateGeneralInfoMutation;

      let variables = {
        activity_id: this.value.id,
        name: this.form.name,
        description: this.form.description,
        with_relationships: false,
      };

      // Add draft only updates.
      if (!this.isActive) {
        if (this.value.anonymous_responses !== this.form.anonymousResponse) {
          variables.anonymous_responses = this.form.anonymousResponse;
        }
        if (this.value.type.id !== this.form.type_id) {
          variables.type_id = this.form.type_id;
        }
        if (this.hasChangesToRelationshipSelection()) {
          variables.with_relationships = true;
          variables.relationships = Object.keys(
            this.form.manualRelationshipSelections
          ).map(manual_relationship_id => {
            return {
              manual_relationship_id: manual_relationship_id,
              selector_relationship_id: this.form.manualRelationshipSelections[
                manual_relationship_id
              ],
            };
          });
        }
      }

      const { data: resultData } = await this.$apollo.mutate({
        mutation,
        variables,
        refetchAll: false,
      });

      return resultData.mod_perform_update_activity.activity;
    },

    /**
     * Get a textual representation of a toggle switch for an active activity (setting is no longer available).
     * @return {string}
     */
    activeToggleText(value) {
      return value
        ? this.$str('boolean_setting_text_enabled', 'mod_perform')
        : this.$str('boolean_setting_text_disabled', 'mod_perform');
    },

    hasUnsavedChanges() {
      return (
        this.form.name !== this.value.edit_name ||
        this.form.description !== this.value.edit_description ||
        this.form.type_id !== this.value.type.id ||
        this.form.anonymousResponse !== this.value.anonymous_responses ||
        this.hasChangesToRelationshipSelection()
      );
    },

    /**
     * Checks if there's been a change to the manual relationship selections.
     *
     * @return {Boolean}
     */
    hasChangesToRelationshipSelection() {
      return this.manualRelationships.some(relationship => {
        return (
          this.form.manualRelationshipSelections[relationship.id] !==
          relationship.selector_relationship_id
        );
      });
    },

    /**
     * Emit an input event with an updated activity object, changes are patched into the existing value (activity).
     *
     * @param {object} update - The new values to patch into the activity object emitted.
     */
    updateActivity(update) {
      let activity = Object.assign({}, this.value, update);
      this.$emit('input', activity);
    },

    /**
     * Displays a warning message if the user tries to navigate away without saving.
     * @param {Event} e
     * @returns {String|void}
     */
    unloadHandler(e) {
      if (!this.hasUnsavedChanges()) {
        return;
      }

      // For older browsers that still show custom message.
      const discardUnsavedChanges = this.$str(
        'unsaved_changes_warning',
        'mod_perform'
      );
      e.preventDefault();
      e.returnValue = discardUnsavedChanges;
      return discardUnsavedChanges;
    },

    /**
     * Reset changes to form.
     */
    resetChanges() {
      this.resetActivityChanges();
      this.resetManualRelationshipChanges();
    },

    /**
     * revert to last saved changes
     */
    resetActivityChanges() {
      this.form.name = this.value.edit_name;
      this.form.description = this.value.edit_description;
      this.form.type_id = this.value.type.id;
      this.form.anonymousResponse = this.value.anonymous_responses;
    },

    /**
     * Reset changes to relationship selection.
     */
    resetManualRelationshipChanges() {
      this.value.manual_relationships.map(relationship => {
        this.form.manualRelationshipSelections[
          relationship.manual_relationship.id
        ] = relationship.selector_relationship.id;
      });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_general_tab_heading",
      "activity_general_response_attribution_heading",
      "activity_general_anonymous_responses_label",
      "boolean_setting_text_enabled",
      "boolean_setting_text_disabled",
      "general_info_label_activity_description",
      "general_info_label_activity_title",
      "general_info_label_activity_type",
      "general_info_participant_selection_description",
      "general_info_participant_selection_heading",
      "save_changes",
      "unsaved_changes_warning"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
