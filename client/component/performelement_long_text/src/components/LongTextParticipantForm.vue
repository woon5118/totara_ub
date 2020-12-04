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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module performelement_long_text
-->

<template>
  <FormScope v-if="loaded" :path="path" :process="process">
    <FormField
      v-slot="{ value: formValue, update: formUpdate }"
      name="response"
      :validations="validations"
      :char-length="50"
      :error="error"
    >
      <WekaWrapper
        v-slot="{ value, update }"
        :value="formValue"
        @update="formUpdate"
      >
        <Weka
          :value="value"
          :usage-identifier="{
            component: 'performelement_long_text',
            area: 'response',
            instanceId: sectionElementId,
          }"
          variant="description"
          :file-item-id="draftFileId"
          :is-logged-in="!isExternalParticipant"
          @input="update"
        />
      </WekaWrapper>
    </FormField>
  </FormScope>
</template>

<script>
import { FormField } from 'tui/components/uniform';
import FormScope from 'tui/components/reform/FormScope';
import Weka from 'editor_weka/components/Weka';
import WekaWrapper from 'performelement_long_text/components/WekaWrapper';
import { v as validation } from 'tui/validation';
// GraphQL queries
import prepareDraftArea from 'performelement_long_text/graphql/prepare_draft_area';

export default {
  components: {
    FormField,
    FormScope,
    Weka,
    WekaWrapper,
  },
  props: {
    path: {
      type: [String, Array],
      default: '',
    },
    error: String,
    isDraft: Boolean,
    element: Object,
    isExternalParticipant: Boolean,
    sectionElementId: {
      type: [String, Number],
      required: true,
    },
    participantInstanceId: {
      type: [String, Number],
      required: true,
    },
  },

  data() {
    return {
      draftFileId: 0,
    };
  },

  computed: {
    /**
     * Have the required queries been loaded?
     * @return {Boolean}
     */
    loaded() {
      return this.draftFileId || this.isExternalParticipant;
    },

    /**
     * An array of validation rules for the element.
     * The rules returned depend on if we are saving as draft or if a response is required or not.
     *
     * @return {(function|object)[]}
     */
    validations() {
      if (!this.isDraft && this.element && this.element.is_required) {
        return [validation.required()];
      }

      return [];
    },
  },

  async mounted() {
    if (this.isExternalParticipant) {
      // File upload is problematic for external participants.
      return;
    }
    await this.$_loadDraftId();
  },

  methods: {
    /**
     * Get a Draft ID to use for temporarily storing uploading files.
     */
    async $_loadDraftId() {
      const {
        data: { draft_id: draftFileId },
      } = await this.$apollo.mutate({
        mutation: prepareDraftArea,
        variables: {
          section_element_id: this.sectionElementId,
          participant_instance_id: this.participantInstanceId,
        },
      });
      this.draftFileId = draftFileId;
    },

    /**
     * Process the form values.
     *
     * @param {Object} value
     * @return {Object|null}
     */
    process(value) {
      if (!value || !value.response) {
        return null;
      }

      return {
        draft_id: this.draftFileId,
        weka: value.response,
      };
    },
  },
};
</script>
