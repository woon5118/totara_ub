<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performAddParticipants">
    <h2 class="tui-performAddParticipants__title">
      {{ $str('add_participants_page_title', 'mod_perform') }}
    </h2>
    <Loader :loading="$apollo.loading">
      <Card class="tui-performAddParticipants__card">
        <h3 class="tui-performAddParticipants__card-title">{{ cardTitle }}</h3>

        <template v-if="!$apollo.loading">
          <Form
            v-if="distinctRelationships.length > 0"
            class="tui-performAddParticipants__form"
          >
            <FormRow
              v-for="relationship in distinctRelationships"
              :key="relationship.id"
              v-slot="{ id, label }"
              :label="relationship.name"
            >
              <InputText
                :id="id"
                :value="getNewParticipantsAsCsv(relationship.id)"
                @input="updateNewParticipants(relationship.id, $event)"
              />
            </FormRow>
          </Form>
          <p v-else>
            {{ $str('manual_participant_add_no_relationships', 'mod_perform') }}
          </p>
        </template>
      </Card>
    </Loader>

    <ButtonGroup class="tui-performAddParticipants__action-buttons">
      <Button
        :styleclass="{ primary: true }"
        :text="$str('save', 'mod_perform')"
        :disabled="isSaving"
        type="submit"
        @click.prevent="showConfirmModal"
      />
      <ActionLink
        :href="goBackLink"
        :text="$str('button_cancel', 'mod_perform')"
        :disabled="isSaving"
      />
    </ButtonGroup>

    <ConfirmationModal
      :open="openConfirmationModal"
      :title="$str('manual_participant_add_confirmation_title', 'mod_perform')"
      :confirm-button-text="$str('button_create', 'mod_perform')"
      :loading="isSaving"
      @confirm="trySave"
      @cancel="closeConfirmModal"
    >
      <p>{{ confirmationMessage }}</p>
    </ConfirmationModal>
  </div>
</template>

<script>
import ActionLink from 'tui/components/links/ActionLink';
import AddParticipantsMutation from 'mod_perform/graphql/add_participants';
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Card from 'tui/components/card/Card';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import InputText from 'tui/components/form/InputText';
import Loader from 'tui/components/loader/Loader';
import subjectInstanceForParticipationQuery from 'mod_perform/graphql/subject_instance_for_participation';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import { notify } from 'tui/notifications';
import { redirectWithPost } from 'mod_perform/redirect';

export default {
  components: {
    ConfirmationModal,
    ActionLink,
    Button,
    ButtonGroup,
    Form,
    FormRow,
    Card,
    Loader,
    InputText,
  },
  props: {
    subjectInstanceId: {
      required: true,
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
  },
  data() {
    return {
      subjectInstance: null,
      newParticipantsByCoreRelationship: {},
      isSaving: false,
      openConfirmationModal: false,
    };
  },
  computed: {
    cardTitle() {
      if (!this.subjectInstance) {
        return '';
      }

      const activity_name = this.subjectInstance.activity.name;
      const subject_name = this.subjectInstance.subject_user.fullname;

      return this.$str('activity_title_for_subject', 'mod_perform', {
        activity_name,
        subject_name,
      });
    },
    distinctRelationships() {
      if (!this.subjectInstance) {
        return [];
      }

      const sectionRelationships = this.subjectInstance.activity.sections.map(
        section => section.section_relationships
      );

      const foundCoreRelationshipIds = {};
      const distinctCoreRelationships = [];

      sectionRelationships.forEach(sectionRelationship => {
        sectionRelationship.forEach(relationship => {
          const coreRelationshipId = relationship.core_relationship.id;

          if (
            !relationship.is_subject &&
            !foundCoreRelationshipIds[coreRelationshipId]
          ) {
            distinctCoreRelationships.push(relationship.core_relationship);
          }
        });
      });

      return distinctCoreRelationships;
    },
    participantInstanceCount() {
      return Object.entries(this.newParticipantsByCoreRelationship).reduce(
        (accumulator, entry) => {
          const userIds = entry[1];
          return accumulator + userIds.length;
        },
        0
      );
    },
    confirmationMessage() {
      if (this.participantInstanceCount === 1) {
        return this.$str(
          'manual_participant_add_confirmation_message_singular',
          'mod_perform'
        );
      }

      return this.$str(
        'manual_participant_add_confirmation_message',
        'mod_perform',
        this.participantInstanceCount
      );
    },
  },
  apollo: {
    subjectInstance: {
      query: subjectInstanceForParticipationQuery,
      variables() {
        return {
          subject_instance_id: this.subjectInstanceId,
        };
      },
      update: data => data['mod_perform_subject_instance'],
    },
  },
  methods: {
    getNewParticipantsAsCsv(coreRelationshipId) {
      if (this.newParticipantsByCoreRelationship[coreRelationshipId]) {
        return this.newParticipantsByCoreRelationship[coreRelationshipId].join(
          ', '
        );
      }

      return '';
    },
    updateNewParticipants(coreRelationshipId, value) {
      const userIds = value
        .split(',')
        .map(userId => userId.trim())
        .filter(userId => userId !== '');

      this.$set(
        this.newParticipantsByCoreRelationship,
        coreRelationshipId,
        userIds
      );
    },
    showConfirmModal() {
      this.openConfirmationModal = true;
    },
    closeConfirmModal() {
      this.openConfirmationModal = false;
    },
    prepareParticipantsDataForSaving() {
      let participantsDataPreparedForSaving = [];
      Object.entries(this.newParticipantsByCoreRelationship).forEach(entry => {
        const coreRelationshipId = entry[0];
        const userIds = entry[1];
        userIds.forEach(userId => {
          participantsDataPreparedForSaving.push({
            core_relationship_id: coreRelationshipId,
            participant_id: userId,
          });
        });
      });
      return participantsDataPreparedForSaving;
    },
    /**
     * Show a generic saving error toast.
     */
    showMutationErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },
    async trySave() {
      this.isSaving = true;

      try {
        const addedParticipantInstances = await this.save();
        redirectWithPost(this.goBackLink, {
          participant_instance_created_count: addedParticipantInstances.length,
        });
      } catch (e) {
        this.showMutationErrorNotification();
        this.isSaving = false;
        this.closeConfirmModal();
      }
    },

    /**
     * Mutation call to add participants.
     * @return {Object}
     */
    async save() {
      const {
        data: {
          mod_perform_add_participants: { participant_instances },
        },
      } = await this.$apollo.mutate({
        mutation: AddParticipantsMutation,
        variables: {
          input: {
            subject_instance_ids: [this.subjectInstanceId],
            participants: this.prepareParticipantsDataForSaving(),
          },
        },
        refetchAll: false, // Don't refetch all the data again
      });

      return participant_instances;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_title_for_subject",
      "add_participants_page_title",
      "button_cancel",
      "button_create",
      "manual_participant_add_confirmation_message",
      "manual_participant_add_confirmation_message_singular",
      "manual_participant_add_confirmation_title",
      "manual_participant_add_no_relationships",
      "save",
      "toast_error_generic_update"
    ]
  }
</lang-strings>
