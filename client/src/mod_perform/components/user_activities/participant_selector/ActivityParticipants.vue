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
  <div class="tui-performUserActivitiesSelectParticipants__instance">
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
      :validate="validate"
      validation-mode="submit"
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
        <FormUserSelector :name="relationship.id" :users="userList" />
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
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import { FormRow, Uniform } from 'totara_core/components/uniform';
import FormUserSelector from 'mod_perform/components/user_activities/participant_selector/FormUserSelector';
import SetManualParticipantsMutation from 'mod_perform/graphql/set_manual_participants';

export default {
  components: {
    Button,
    ButtonGroup,
    FormRow,
    FormUserSelector,
    Uniform,
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
      if (this.subjectInstance.subject_user.id == this.currentUserId) {
        // We always show the subject user in the list if they are logged in.
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
     * Handle submission of the subject instance participants.
     *
     * @param {Object} users The submitted form data.
     */
    async submit(users) {
      this.isSaving = true;

      const participants = Object.keys(users).map(relationship_id => {
        return {
          manual_relationship_id: relationship_id,
          user_ids: users[relationship_id].map(user => user.id),
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
     * Get form errors if each relationship doesn't have at least one participant.
     *
     * @param {Object} users The submitted form data.
     * @returns {Object} validation errors
     */
    validate(users) {
      const errors = {};
      Object.keys(users).forEach(id => {
        if (users[id].length === 0) {
          errors[id] = this.$str(
            'error_no_participants_selected',
            'mod_perform'
          );
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
      });
      return values;
    },

    /**
     * Check and store whether there have been changes.
     *
     * @param {Object} users The form data.
     * @returns {boolean}
     */
    updateHasChanges(users) {
      this.hasChanges = Object.keys(users).some(id => users[id].length > 0);
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
      "error_no_participants_selected",
      "unsaved_changes_warning",
      "user_activities_created_at",
      "user_activities_select_participants_subject_instance_title"
    ],
    "totara_core": [
      "save"
    ]
  }
</lang-strings>
