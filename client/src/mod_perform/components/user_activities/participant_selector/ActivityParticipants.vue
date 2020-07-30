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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <Card
    :clickable="false"
    class="tui-performUserActivitiesSelectParticipants__instance"
  >
    <h3 class="tui-performUserActivitiesSelectParticipants__instance-title">
      {{ instanceTitle }}
    </h3>

    <div class="tui-performUserActivitiesSelectParticipants__instance-meta">
      <p>
        {{
          $str(
            'user_activities_created_at',
            'mod_perform',
            subjectInstance.created_at
          )
        }}
      </p>
    </div>

    <Uniform
      :initial-values="initialValues"
      :validate="validateExternal"
      input-width="full"
      class="tui-performUserActivitiesSelectParticipants__instance-form"
      @change="updateHasChanges"
      @submit="submit"
    >
      <FormRow
        v-for="relationship in relationships"
        :key="relationship.id"
        :label="relationship.name"
        :required="true"
      >
        <ExternalUserSelector
          v-if="isExternal(relationship)"
          :name="relationship.id"
        />

        <FormField
          v-else
          v-slot="{ id, value, update }"
          :name="relationship.id"
          :validate="validateInternal"
        >
          <UserSelector v-model="value" :users="userList" @input="update" />
        </FormField>
      </FormRow>

      <FormRow>
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :text="$str('save', 'totara_core')"
            :disabled="isSaving"
            type="submit"
          />
        </ButtonGroup>
      </FormRow>
    </Uniform>
  </Card>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Card from 'tui/components/card/Card';
import ExternalUserSelector from 'mod_perform/components/user_activities/participant_selector/ExternalUserSelector';
import UserSelector from 'mod_perform/components/user_activities/participant_selector/UserSelector';
import { FormField, FormRow, Uniform } from 'tui/components/uniform';
import SetManualParticipantsMutation from 'mod_perform/graphql/set_manual_participants';
import { RELATIONSHIP_PERFORM_EXTERNAL } from 'mod_perform/constants';

export default {
  components: {
    Button,
    ButtonGroup,
    Card,
    ExternalUserSelector,
    FormField,
    FormRow,
    Uniform,
    UserSelector,
  },

  props: {
    subjectInstance: {
      required: true,
      type: Object,
    },
    relationships: {
      required: true,
      type: Array,
    },
    users: {
      required: true,
      type: Array,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      initialValues: this.getInitialValues(),
      hasChanges: false,
      isSaving: false,
    };
  },

  computed: {
    /**
     * Get the title to display for this user's activity.
     *
     * @returns {String}
     */
    instanceTitle() {
      return this.$str(
        'user_activities_select_participants_subject_instance_title',
        'mod_perform',
        {
          activity: this.subjectInstance.activity.name,
          user: this.subjectInstance.subject_user.fullname,
        }
      );
    },

    /**
     * Get the users that we can select for this instance.
     *
     * @returns {Object[]}
     */
    userList() {
      if (
        this.subjectInstance.subject_user.id == this.currentUserId &&
        this.users.length < 2
      ) {
        // If the subject user is logged in and there aren't multiple users to
        // pick from, then we include the subject user in the list of users.
        // This is to avoid the potential issue of a subject not being able to
        // select any users because they only have permission to see themselves.
        return this.users;
      }

      // Otherwise we do not want to be able to select the subject instance.
      return this.users.filter(
        user => user.id !== this.subjectInstance.subject_user.id
      );
    },
  },

  mounted() {
    // Confirm navigation away if user has made a selection without saving.
    window.addEventListener('beforeunload', this.unloadHandler);
  },

  beforeDestroy() {
    // Remove the navigation warning otherwise it may appear despite this component not existing!
    window.removeEventListener('beforeunload', this.unloadHandler);
  },

  methods: {
    /**
     * Is the specified relationship for external participants?
     *
     * @param {Object|Number} relationship
     * @returns {Boolean}
     */
    isExternal(relationship) {
      if (!isNaN(relationship)) {
        // Find the relationship object from it's ID.
        relationship = this.relationships
          .filter(r => {
            return r.id === relationship;
          })
          .shift();
      }

      return relationship.idnumber === RELATIONSHIP_PERFORM_EXTERNAL;
    },

    /**
     * Handle submission of the subject instance participants.
     *
     * @param {Object} data The submitted form data.
     */
    async submit(data) {
      this.isSaving = true;

      const participants = Object.keys(data).map(relationshipId => {
        return {
          manual_relationship_id: relationshipId,
          users: data[relationshipId].map(user => {
            if (this.isExternal(relationshipId)) {
              return user;
            }
            return {
              user_id: user.id,
            };
          }),
        };
      });

      try {
        await this.$apollo.mutate({
          mutation: SetManualParticipantsMutation,
          variables: {
            subject_instance_id: this.subjectInstance.id,
            participants,
          },
        });
        this.hasChanges = false;
        this.$emit('submit');
      } catch (e) {
        this.hasChanges = false;
        this.$emit('error');
      }

      this.isSaving = false;
    },

    /**
     * Get form errors if an internal relationship doesn't have at least one participant.
     *
     * @param {Object} values The submitted form data.
     * @returns {Object} validation errors
     */
    validateInternal(values) {
      if (values.length === 0) {
        return this.$str('error_no_participants_selected', 'mod_perform');
      }
      return false;
    },

    /**
     * Get form errors if an external relationship has duplicate participant emails.
     *
     * @param {Object} values The submitted form data.
     * @returns {Object} validation errors
     */
    validateExternal(values) {
      const errors = {};
      Object.keys(values).forEach(relationshipId => {
        if (this.isExternal(relationshipId)) {
          errors[relationshipId] = {};
          let externalValues = values[relationshipId];
          let emails = [];
          Object.keys(externalValues).forEach(formId => {
            errors[relationshipId][formId] = {};
            let user = externalValues[formId];
            if (emails.includes(user.email)) {
              errors[relationshipId][formId].email = this.$str(
                'error_external_participant_duplicate_email',
                'mod_perform'
              );
            } else {
              emails.push(user.email);
            }
          });
        }
      });
      return errors;
    },

    /**
     * Get the initial form data.
     *
     * Since a user can only select participants once, we don't need to set it to previously entered data.
     * So we just populate it with empty arrays.
     *
     * @returns {Object}
     */
    getInitialValues() {
      let values = {};
      this.relationships.forEach(relationship => {
        values[relationship.id] = [];
        if (this.isExternal(relationship)) {
          values[relationship.id] = [
            {
              email: '',
              name: '',
            },
          ];
        }
      });
      return values;
    },

    /**
     * Check and store whether there have been changes.
     *
     * @param {Object} values The form data.
     * @returns {boolean}
     */
    updateHasChanges(values) {
      this.hasChanges = Object.keys(values).some(relationship => {
        if (this.isExternal(relationship)) {
          return values[relationship].some(user => {
            return user.name.length > 0 || user.email.length > 0;
          });
        } else {
          return values[relationship].length > 0;
        }
      });
    },

    /**
     * Displays a warning message if the user tries to navigate away without saving.
     *
     * @param {Event} e
     * @returns {String|void}
     */
    unloadHandler(e) {
      if (!this.hasChanges) {
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
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "error_external_participant_duplicate_email",
      "error_no_participants_selected",
      "external_user_email",
      "external_user_email_help",
      "external_user_name",
      "external_user_name_help",
      "unsaved_changes_warning",
      "user_activities_created_at",
      "user_activities_select_participants_subject_instance_title"
    ],
    "totara_core": [
      "save"
    ]
  }
</lang-strings>
