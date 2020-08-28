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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module mod_perform
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

          <p v-if="requiredText" aria-hidden="true">
            <span class="tui-performEditSectionContentModal__required">*</span>
            {{ $str('required_fields', 'mod_perform') }}
          </p>

          <Loader :loading="isLoading">
            <div
              v-if="isDraft"
              class="tui-performEditSectionContentModal__form"
            >
              <Droppable
                v-slot="{
                  attrs,
                  events,
                  isActive,
                  isDropValid,
                  dropTarget,
                  placeholder,
                }"
                :source-id="$id('element-list')"
                source-name="Element List"
                :accept-drop="validateDropElement"
                :reorder-only="true"
                @drop="handleDropElement"
              >
                <div
                  class="tui-performEditSectionContentModal__dragList"
                  v-bind="attrs"
                  v-on="events"
                >
                  <render :vnode="dropTarget" />
                  <Draggable
                    v-for="(sectionElement, index) in sectionElements"
                    :key="sectionElement.id"
                    v-slot="{ dragging, attrs, events, moveMenu, anyDragging }"
                    :index="index"
                    :value="sectionElement.id"
                    type="element"
                    :disabled="!validDragElement(sectionElement)"
                  >
                    <div
                      class="tui-performEditSectionContentModal__draggableItem"
                      :class="{
                        'tui-performEditSectionContentModal__draggableItem--dragging': dragging,
                      }"
                      v-bind="attrs"
                      v-on="events"
                    >
                      <div
                        v-if="
                          (!anyDragging || dragging) &&
                            validDragElement(sectionElement)
                        "
                        class="tui-performEditSectionContentModal__draggableItem-moveIcon"
                      >
                        <DragHandleIcon />
                      </div>
                      <render :vnode="moveMenu" />
                      <component
                        :is="componentFor(sectionElement)"
                        ref="sectionElements"
                        :key="sectionElement.id"
                        :section-id="sectionId"
                        :element-id="sectionElement.element.id"
                        :data="sectionElement.element.data"
                        :raw-data="sectionElement.element.raw_data"
                        :title="sectionElement.element.title"
                        :raw-title="sectionElement.element.raw_title"
                        :identifier="sectionElement.element.identifier"
                        :is-required="sectionElement.element.is_required"
                        :type="sectionElement.element.type"
                        :error="errors[sectionElement.id]"
                        :activity-state="activityState"
                        @update="update(sectionElement, $event, index)"
                        @edit="edit(sectionElement)"
                        @display="display(sectionElement)"
                        @display-read="displayReadOnly(sectionElement)"
                        @remove="tryDelete(sectionElement, index)"
                      />
                    </div>
                  </Draggable>
                  <render :vnode="placeholder" />
                </div>
              </Droppable>
              <ContentAddElementButton @add-element-item="add" />
            </div>
            <div v-else class="tui-performEditSectionContentModal__form">
              <component
                :is="componentFor(sectionElement)"
                v-for="(sectionElement, index) in sectionElements"
                ref="sectionElements"
                :key="sectionElement.id"
                :data="sectionElement.element.data"
                :raw-data="sectionElement.element.raw_data"
                :title="sectionElement.element.title"
                :raw-title="sectionElement.element.raw_title"
                :identifier="
                  normaliseIdentifierForElements(
                    sectionElement.element.identifier
                  )
                "
                :is-required="sectionElement.element.is_required"
                :type="sectionElement.element.type"
                :error="errors[sectionElement.id]"
                :activity-state="activityState"
                @update="update(sectionElement, $event, index)"
                @edit="edit(sectionElement)"
                @display="display(sectionElement)"
                @display-read="displayReadOnly(sectionElement)"
                @remove="tryDelete(sectionElement, index)"
              />
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
import Button from 'tui/components/buttons/Button';
import ButtonCancel from 'tui/components/buttons/Cancel';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import ButtonSubmit from 'tui/components/buttons/Submit';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import ContentAddElementButton from 'mod_perform/components/manage_activity/content/ContentAddElementButton';
import Draggable from 'tui/components/drag_drop/Draggable';
import Droppable from 'tui/components/drag_drop/Droppable';
import DragHandleIcon from 'tui/components/icons/DragHandle';
import Loader from 'tui/components/loader/Loader';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import sectionDetailQuery from 'mod_perform/graphql/section_admin';
import updateSectionElementMutation from 'mod_perform/graphql/update_section_elements';
import { notify } from 'tui/notifications';
import { pull, uniqueId } from 'tui/util';
import { ACTIVITY_STATUS_DRAFT } from 'mod_perform/constants';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    ConfirmationModal,
    ContentAddElementButton,
    Draggable,
    Droppable,
    DragHandleIcon,
    Loader,
    Modal,
    ModalContent,
    ModalPresenter,
  },

  props: {
    activityState: {
      type: Object,
      required: true,
    },
    sectionId: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    requiredText: Boolean,
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
      readOnlyIds: [],
      removeIds: [],
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

    /**
     * Is the activity a draft, and thus can be modified?
     *
     * @return {Boolean}
     */
    isDraft() {
      return this.activityState.name === ACTIVITY_STATUS_DRAFT;
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
      this.sectionElements = [];
      this.unsavedChangesModalOpen = false;
      this.isOpen = false;
    },

    /**
     * Add new plugin element.
     */
    add(plugin) {
      const sectionElement = {
        id: 'unsaved-' + uniqueId(),
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
            sort_order: this.getSortOrder(index),
          }),
        ];

        // Increment the sort order of every saved element after this element by 1.
        toSave.move = this.sectionElements
          .slice(index + 1)
          .filter(this.elementExists)
          .map(element => {
            return {
              section_element_id: element.id,
              sort_order: element.sort_order + 1,
            };
          });
      } else {
        toSave.update = [
          Object.assign(elementToSave, {
            element_id: sectionElement.element.id,
          }),
        ];
      }
      this.save(toSave, this.$str('toast_success_save_element', 'mod_perform'));

      this.display(sectionElement);
    },

    /**
     * Has the specified element not been saved into the DB?
     */
    elementDoesNotExist(element) {
      return 'creating' in element;
    },

    /**
     * Has the specified element been saved into the DB?
     */
    elementExists(element) {
      return !this.elementDoesNotExist(element);
    },

    /**
     * Calculate the sort order value for a section element.
     *
     * @param {Number} index
     */
    getSortOrder(index) {
      const savedSectionElementsBefore = this.sectionElements
        .slice(0, index)
        .filter(this.elementExists);

      return savedSectionElementsBefore.length + 1;
    },

    /**
     * Add element into edit list
     */
    edit(sectionElement) {
      this.editingIds.push(sectionElement.id);
    },

    /**
     * Remove element from the edit list.
     */
    stopEditing(sectionElement) {
      pull(this.editingIds, sectionElement.id);
    },

    /**
     * Is the element currently being edited?
     */
    isEditing(element) {
      return this.editingIds.includes(element.id);
    },

    /**
     * Display section element
     * Remove creating view if section element move to display mode
     */
    display(sectionElement) {
      if (this.isDraft) {
        this.stopEditing(sectionElement);
        if (sectionElement.creating) {
          this.remove(sectionElement);
        }
      } else {
        pull(this.readOnlyIds, sectionElement.id);
      }
    },

    /**
     * Display Read only section element when activity in active mode
     * @param {Object} sectionElement
     *
     */
    displayReadOnly(sectionElement) {
      this.readOnlyIds.push(sectionElement.id);
    },

    /**
     * Is the element in a read-only state?
     */
    isReadOnly(element) {
      return this.readOnlyIds.includes(element.id);
    },

    /**
     * Remove section element
     * if section element already saved update remove list
     */
    remove(sectionElement) {
      this.stopEditing(sectionElement);
      pull(this.sectionElements, sectionElement);
    },

    /**
     * Try delete the section element.
     * If it exists in the DB, show a confirmation before deleting.
     * @param {Object} sectionElement
     * @param {Number} index
     */
    tryDelete(sectionElement, index) {
      if (sectionElement.element.id) {
        this.deleteModalOpen = true;
        this.elementToDelete = sectionElement;
        this.elementToDelete.index = index;
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
        .slice(this.elementToDelete.index + 1)
        .filter(this.elementExists)
        .map((element, index) => {
          return {
            section_element_id: element.id,
            sort_order: this.elementToDelete.sort_order + index,
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
      this.isSaving = false;
    },

    /**
     * Reorder elements
     */
    async reorderElements(sectionElements) {
      this.isSaving = true;
      const toSave = {};
      toSave.move = sectionElements
        .filter(this.elementExists)
        .map((element, index) => {
          return {
            section_element_id: element.id,
            sort_order: index + 1,
          };
        });

      await this.save(toSave, null);

      this.isSaving = false;
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
      if (this.isReadOnly(sectionElement)) {
        return tui.asyncComponent(type.admin_read_only_display_component);
      }
      if (this.isEditing(sectionElement)) {
        return tui.asyncComponent(type.admin_form_component);
      }
      return tui.asyncComponent(type.admin_display_component);
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
        if (saveNotificationMessage) {
          this.showSuccessNotification(saveNotificationMessage);
        }
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
     *
     * @param {Array} data GraphQL data
     */
    updateSectionElementData(data) {
      const elements = data.map(item => {
        // Don't reset the element data if it is being edited.
        const existingElement = this.sectionElements.find(
          element => element.id === item.id
        );
        if (existingElement && this.isEditing(existingElement)) {
          return existingElement;
        }

        return Object.assign({}, item, {
          element: Object.assign({}, item.element, {
            type: item.element.element_plugin,
            data: JSON.parse(item.element.data),
            raw_data: JSON.parse(item.element.raw_data),
          }),
        });
      });

      const unsavedElements = this.sectionElements.filter(
        this.elementDoesNotExist
      );

      this.sectionElements = elements.concat(unsavedElements).sort((a, b) => {
        const sortDiff = a.sort_order - b.sort_order;
        if (sortDiff !== 0) {
          return sortDiff;
        } else {
          // There can be duplicate sort orders because the back end must always have sequential orders,
          // but in the front end there could be an unsaved element that we want to place between two
          // saved elements. We should show the unsaved element before the saved one in this case.
          return this.elementDoesNotExist(a) ? -1 : 1;
        }
      });
    },

    /**
     * Show a generic saving success toast.
     *
     * @param {String} messageString Override the message text.
     */
    showSuccessNotification(messageString) {
      notify({
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
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * check whether a drag is allowed.
     *
     * @param {Array} sectionElements
     */
    validDragElement(sectionElement) {
      return !this.isEditing(sectionElement) && this.sectionElements.length > 1;
    },

    /**
     * check whether a drop is allowed.
     *
     * @param {DropInfo} info
     */
    validateDropElement(info) {
      return info.destination.sourceId == info.source.sourceId;
    },

    /**
     * Called when element is dropped on a list.
     *
     * @param {DropInfo} info
     */
    handleDropElement(info) {
      if (info.destination.sourceId == info.source.sourceId) {
        //reorder elements
        const item = this.sectionElements.splice(info.source.index, 1)[0];
        this.sectionElements.splice(info.destination.index, 0, item);

        this.reorderElements(this.sectionElements);
      }
    },

    /**
     * Check element editable
     */
    isElementEditable() {
      return this.activityState.name !== 'ACTIVE';
    },

    /**
     * Replace null with empty string before passing it on to the element components because they shouldn't
     * bother about handling null value.
     *
     * @param {String} identifier
     */
    normaliseIdentifierForElements(identifier) {
      return identifier === null ? '' : identifier;
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
    "required_fields",
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
