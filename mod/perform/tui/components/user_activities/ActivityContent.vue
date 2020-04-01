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
  <Uniform
    v-slot="{ getSubmitting }"
    :initial-values="initialValues"
    @submit="submit"
  >
    <h3>
      {{ section.title }}
    </h3>
    <component
      :is="componentFor(sectionElement)"
      v-for="sectionElement in sectionElements"
      :key="sectionElement.id"
      :path="['sectionElements', sectionElement.id]"
      :data="sectionElement.element.data"
      :name="sectionElement.element.name"
      :type="sectionElement.element.type"
      :error="errors && errors[sectionElement.clientId]"
    />
    <ButtonGroup>
      <ButtonSubmit :submitting="getSubmitting()" />
      <ButtonCancel @click="cancel" />
    </ButtonGroup>
  </Uniform>
</template>

<script>
import { uniqueId } from 'totara_core/util';
import { notify } from 'totara_core/notifications';
import { Uniform } from 'totara_core/components/uniform';
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonSubmit from 'totara_core/components/buttons/Submit';
import sectionResponses from 'mod_perform/graphql/participant_section';
import updateSectionResponsesMutation from 'mod_perform/graphql/update_section_responses';

const TOAST_DURATION = 10 * 1000; // in microseconds.

export default {
  components: {
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    Uniform,
  },

  props: {
    subjectInstanceId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      initialValues: { sectionElements: {} },
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      errors: null,
      isSaving: false,
      participantSectionId: null,
    };
  },

  apollo: {
    section: {
      query: sectionResponses,
      variables() {
        return {
          subject_instance_id: this.subjectInstanceId,
        };
      },
      update: data => data.mod_perform_participant_section.section,
      fetchPolicy: 'network-only',
      result({ data }) {
        this.participantSectionId = data.mod_perform_participant_section.id;
        this.sectionElements = data.mod_perform_participant_section.section.section_elements.map(
          item => {
            return {
              id: item.id,
              clientId: uniqueId(),
              element: {
                type: item.element.element_plugin,
                name: item.element.title,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
              },
              sort_order: item.sort_order,
            };
          }
        );
        this.initialValues.sectionElements = this.sectionElements;
      },
    },
  },

  methods: {
    /**
     * if the element is editing shows the Form component else shows element display component
     */
    componentFor(sectionElement) {
      const { type } = sectionElement.element;
      return tui.asyncComponent(type.participant_form_component);
    },

    /**
     * cancel saving
     */
    cancel() {
      this.backToUserActivities();
    },

    /**
     * Show a generic success toast.
     */
    showSuccessNotification() {
      notify({
        duration: TOAST_DURATION,
        message: this.$str('toast_success_save_response', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: TOAST_DURATION,
        message: this.$str('toast_error_save_response', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Back to user activities
     */
    backToUserActivities() {
      window.history.back();
    },

    /**
     * Save user responses and show notifications
     */
    async submit(values) {
      if (this.errors) {
        this.errors = null;
      }

      // assign values from submission to the section elements
      this.sectionElements.forEach(sectionElement => {
        const result = values.sectionElements[sectionElement.id];
        sectionElement.element.data = result;
      });

      this.isSaving = true;
      try {
        const sectionResponsesResult = await this.save();
        const element_responses =
          sectionResponsesResult.mod_perform_update_section_responses
            .element_responses;

        //assign errors to individual elements
        this.errors = element_responses
          .filter(item => item.validation_errors)
          .reduce((acc, cur) => {
            cur.validation_errors.forEach(error => {
              acc[cur.section_element.id] = error.error_message;
            });
            return acc;
          }, null);

        //show validation if no errors
        if (!this.errors) {
          this.showSuccessNotification();
        }
        this.isSaving = false;
      } catch (e) {
        this.showErrorNotification();
        this.isSaving = false;
      } finally {
        this.isSaving = false;
      }
    },

    /**
     * Extract section elements into new. update , delete and move
     * and call the GQL mutation to save section elements
     * @returns {Promise<any>}
     */
    async save() {
      let variables,
        update = [];

      this.sectionElements.forEach(function(item) {
        update.push({
          section_element_id: item.id,
          response_data: JSON.stringify(item.element.data),
        });
      });
      variables = {
        input: {
          participant_section_id: this.participantSectionId,
          update: update,
        },
      };

      const { data: resultData } = await this.$apollo.mutate({
        mutation: updateSectionResponsesMutation,
        variables: variables,
        refetchAll: false,
      });

      return resultData;
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform": [
    "toast_success_save_response",
    "toast_error_save_response"
  ]
  }
</lang-strings>
