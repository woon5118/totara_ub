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

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->

<template>
  <span>
    <slot name="trigger" :open="openThis" />

    <ModalPresenter :open="isOpen" @request-close="tryCloseThis">
      <Modal
        size="sheet"
        :aria-labelledby="$id('title')"
        :dismissable="{ backdropClick: false }"
      >
        <ModalContent>
          <template v-slot:title>
            <h2 class="tui-performEditSectionContentModal__title">
              {{ title }}
            </h2>
          </template>

          <Loader :loading="isLoading">
            <div class="tui-performEditSectionContentModal__form">
              <component
                :is="componentFor(sectionElement)"
                v-for="(sectionElement, index) in sectionElements"
                ref="sectionElements"
                :key="sectionElement.clientId"
                :data="sectionElement.element.data"
                :raw-data="sectionElement.element.raw_data"
                :title="sectionElement.element.title"
                :raw-title="sectionElement.element.raw_title"
                :identifier="sectionElement.element.identifier"
                :is-required="sectionElement.element.is_required"
                :type="sectionElement.element.type"
                :error="errors[sectionElement.clientId]"
                @update="update(sectionElement, $event, index)"
                @edit="edit(sectionElement)"
                @display="display(sectionElement)"
                @remove="tryDelete(sectionElement)"
              />

              <ContentAddElementButton @add-element-item="add" />
            </div>
          </Loader>

          <template v-slot:buttons>
            <Button
              :id="$id('edit-content-close')"
              :text="$str('button_close', 'mod_perform')"
              @click="tryCloseThis"
            />
          </template>
        </ModalContent>
      </Modal>
    </ModalPresenter>

    <ConfirmationModal
      :open="deleteModalOpen"
      :close-button="false"
      :title="$str('modal_element_delete_title', 'mod_perform')"
      :confirm-button-text="$str('delete')"
      :loading="isSaving"
      @confirm="deleteSelectedElement"
      @cancel="closeDeleteModal"
    >
      <p>{{ $str('modal_element_delete_message', 'mod_perform') }}</p>
    </ConfirmationModal>

    <ConfirmationModal
      :open="unsavedChangesModalOpen"
      :close-button="false"
      :title="$str('modal_element_unsaved_changes_title', 'mod_perform')"
      :confirm-button-text="$str('button_close', 'mod_perform')"
      @confirm="closeThis"
      @cancel="unsavedChangesModalOpen = false"
    >
      <p>{{ $str('modal_element_unsaved_changes_message', 'mod_perform') }}</p>
    </ConfirmationModal>
  </span>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonSubmit from 'totara_core/components/buttons/Submit';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import ContentAddElementButton from 'mod_perform/components/manage_activity/content/ContentAddElementButton';
import Loader from 'totara_core/components/loader/Loader';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import sectionDetailQuery from 'mod_perform/graphql/section_admin';
import updateSectionElementMutation from 'mod_perform/graphql/update_section_elements';
import { notify } from 'totara_core/notifications';
import { pull, uniqueId } from 'totara_core/util';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    ConfirmationModal,
    ContentAddElementButton,
    Loader,
    Modal,
    ModalContent,
    ModalPresenter,
  },

  props: {
    sectionId: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      isOpen: false,
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      editingIds: [],
      errors: {},
      isSaving: false,
      deleteModalOpen: false,
      elementToDelete: null,
      unsavedChangesModalOpen: false,
      skipQuery: true,
    };
  },

  apollo: {
    section: {
      query: sectionDetailQuery,
      variables() {
        return { section_id: this.sectionId };
      },
      update: data => data.mod_perform_section_admin,
      fetchPolicy: 'network-only',
      result({ data }) {
        this.updateSectionElementData(
          data.mod_perform_section_admin.section_elements
        );
      },
      skip() {
        return this.skipQuery;
      },
    },
  },

  computed: {
    /**
     * Are we currently mutating or querying data via graphQL?
     *
     * @return {Boolean}
     */
    isLoading() {
      return this.$apollo.loading || this.isSaving;
    },

    /**
     * Are there any elements still being edited?
     *
     * @return {Boolean}
     */
    hasUnsavedChanges() {
      return this.editingIds.length > 0;
    },
  },

  mounted() {
    // Confirm navigation away if user is currently editing.
    window.addEventListener('beforeunload', this.unloadHandler);
  },

  beforeDestroy() {
    // Modal will no longer exist so remove the navigation warning.
    window.removeEventListener('beforeunload', this.unloadHandler);
  },

  methods: {
    /**
     * Open this modal.
     */
    openThis() {
      // Manually execute the query so data is refreshed every time the modal is opened.
      this.skipQuery = false;
      this.$apollo.queries.section.refresh();
      this.isOpen = true;
    },

    /**
     * Attempt to close this modal.
     * Ask the user to confirm if there are elements still being edited.
     */
    tryCloseThis() {
      if (this.hasUnsavedChanges) {
        this.unsavedChangesModalOpen = true;
      } else {
        this.closeThis();
      }
    },

    /**
     * Close this modal.
     */
    closeThis() {
      // Prevent query from running when modal is closed.
      this.skipQuery = true;
      this.editingIds = [];
      this.unsavedChangesModalOpen = false;
      this.isOpen = false;
    },

    /**
     * Add new plugin element.
     */
    add(plugin) {
      const sectionElement = {
        id: this.sectionId,
        clientId: uniqueId(),
        element: {
          id: null,
          type: plugin,
          title: '',
          raw_title: '',
          identifier: '',
          data: {},
          raw_data: {},
        },
        sort_order: this.sectionElements.length + 1,
        creating: true,
      };

      this.sectionElements.push(sectionElement);
      this.edit(sectionElement);
    },

    /**
     * Update existing elements and shows display view of the element.
     */
    update(sectionElement, elementData, index) {
      sectionElement.element = Object.assign(
        sectionElement.element,
        elementData,
        {
          raw_title: elementData.title,
          raw_data: elementData.data,
        }
      );

      const elementToSave = {
        title: sectionElement.element.raw_title,
        data: JSON.stringify(sectionElement.element.raw_data),
      };

      // 'identifier' and 'is_required' attributes don't exist for static elements so need to handle them.
      if (sectionElement.element.identifier != null) {
        elementToSave.identifier = sectionElement.element.identifier;
      }
      if (sectionElement.element.is_required != null) {
        elementToSave.is_required = sectionElement.element.is_required;
      }

      const toSave = {};
      if (sectionElement.creating) {
        delete sectionElement.creating;
        toSave.create_new = [
          Object.assign(elementToSave, {
            plugin_name: sectionElement.element.type.plugin_name,
            sort_order: index + 1,
          }),
        ];
      } else {
        toSave.update = [
          Object.assign(elementToSave, {
            element_id: sectionElement.element.id,
          }),
        ];
      }
      this.save(toSave);

      this.display(sectionElement);
    },

    /**
     * Add element into edit list
     */
    edit(sectionElement) {
      this.editingIds.push(sectionElement.clientId);
    },

    /**
     * Remove element from the edit list.
     */
    stopEditing(sectionElement) {
      pull(this.editingIds, sectionElement.clientId);
    },

    /**
     * Display section element
     * Remove creating view if section element move to display mode
     */
    display(sectionElement) {
      this.stopEditing(sectionElement);
      if (sectionElement.creating) {
        this.remove(sectionElement);
      }
    },

    /**
     * Remove section element from the display list.
     * @param {Object} sectionElement
     */
    remove(sectionElement) {
      this.stopEditing(sectionElement);
      pull(this.sectionElements, sectionElement);
    },

    /**
     * Try delete the section element.
     * If it exists in the DB, show a confirmation before deleting.
     * @param {Object} sectionElement
     */
    tryDelete(sectionElement) {
      if (sectionElement.element.id) {
        this.deleteModalOpen = true;
        this.elementToDelete = sectionElement;
      } else {
        this.remove(sectionElement);
      }
    },

    /**
     * Trigger mutation to delete the element from the DB.
     */
    async deleteSelectedElement() {
      this.isSaving = true;

      // Need to recalculate the sort orders if deleting
      const move = this.sectionElements
        .filter(element => element.id !== this.elementToDelete.id)
        .map((element, index) => {
          return {
            section_element_id: element.id,
            sort_order: index + 1,
          };
        });

      await this.save(
        {
          move,
          delete: [
            {
              section_element_id: this.elementToDelete.id,
            },
          ],
        },
        this.$str('toast_success_delete_element', 'mod_perform')
      );
      this.remove(this.elementToDelete);
      this.closeDeleteModal();
    },

    /**
     * Close element deletion confirmation modal.
     */
    closeDeleteModal() {
      this.deleteModalOpen = false;
      this.elementToDelete = null;
    },

    /**
     * if the element is editing shows the Form component else shows element display component
     */
    componentFor(sectionElement) {
      const { type } = sectionElement.element;
      const isEditing = this.editingIds.includes(sectionElement.clientId);
      return tui.asyncComponent(
        isEditing ? type.admin_form_component : type.admin_display_component
      );
    },

    /**
     * Try to persist the activity elements to the back end
     * Shows toasts and emits events on success/failure.
     *
     * @param {Object} variables
     * @param {String} [saveNotificationMessage] Override text that is shown in the success notification.
     */
    async save(variables, saveNotificationMessage) {
      this.isSaving = true;

      try {
        const { data: result } = await this.$apollo.mutate({
          mutation: updateSectionElementMutation,
          variables: {
            input: Object.assign(variables, {
              section_id: this.sectionId,
            }),
          },
          refetchAll: false,
        });
        const section = result.mod_perform_update_section_elements.section;
        this.updateSectionElementData(section.section_elements);
        this.$emit('update-summary', section);
        this.showSuccessNotification(saveNotificationMessage);
        this.isSaving = false;
      } catch (e) {
        this.showErrorNotification();
        // If something goes wrong during create, allow the user to try again.
        this.isSaving = false;
      }
    },

    /**
     * Displays a warning message if the user tries to navigate away without saving.
     * @param {Event} e
     * @returns {String|void}
     */
    unloadHandler(e) {
      if (!this.hasUnsavedChanges) {
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
     * Process and apply the section element data from gql for use within vue.
     */
    updateSectionElementData(data) {
      this.sectionElements = data.map(item => {
        return Object.assign({}, item, {
          clientId: uniqueId(),
          element: Object.assign({}, item.element, {
            type: item.element.element_plugin,
            data: JSON.parse(item.element.data),
            raw_data: JSON.parse(item.element.raw_data),
          }),
        });
      });
    },

    /**
     * Show a generic saving success toast.
     *
     * @param {String} messageString Override the message text.
     */
    showSuccessNotification(messageString) {
      notify({
        duration: NOTIFICATION_DURATION,
        message:
          messageString ||
          this.$str('toast_success_save_element', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },
  },
};
</script>

<lang-strings>
{
  "mod_perform": [
    "button_close",
    "modal_element_delete_message",
    "modal_element_delete_title",
    "modal_element_unsaved_changes_message",
    "modal_element_unsaved_changes_title",
    "toast_error_generic_update",
    "toast_success_delete_element",
    "toast_success_save_element",
    "unsaved_changes_warning"
  ],
  "moodle": [
    "delete"
  ]
}
</lang-strings>
