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
  <Modal
    size="sheet"
    :aria-labelledby="$id('title')"
    :dismissable="{ backdropClick: false }"
  >
    <ModalContent :title="section.title">
      <div class="tui-performEditSectionContentModal__form">
        <component
          :is="componentFor(sectionElement)"
          v-for="sectionElement in sectionElements"
          ref="sectionElements"
          :key="sectionElement.clientId"
          :data="sectionElement.element.data"
          :raw-data="sectionElement.element.raw_data"
          :title="sectionElement.element.title"
          :raw-title="sectionElement.element.raw_title"
          :type="sectionElement.element.type"
          :error="errors[sectionElement.clientId]"
          @update="update(sectionElement, $event)"
          @edit="edit(sectionElement)"
          @display="display(sectionElement)"
          @remove="remove(sectionElement)"
        />

        <ContentAddElementButton @add-element-item="add" />
        <ButtonGroup>
          <ButtonSubmit :disabled="!canSubmit()" @click.prevent="trySave" />
          <ButtonCancel @click="cancel" />
        </ButtonGroup>
      </div>
    </ModalContent>
  </Modal>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ButtonSubmit from 'totara_core/components/buttons/Submit';
import ContentAddElementButton from 'mod_perform/components/manage_activity/content/ContentAddElementButton';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import sectionDetailQuery from 'mod_perform/graphql/section_admin';
import updateSectionElementMutation from 'mod_perform/graphql/update_section_elements.graphql';
import { pull, uniqueId } from 'totara_core/util';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    ButtonSubmit,
    ContentAddElementButton,
    Modal,
    ModalContent,
  },
  props: {
    sectionId: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      section: {
        title: '',
        section_elements: [],
      },
      sectionElements: [],
      editingIds: [],
      removeIds: [],
      errors: {},
      isSaving: false,
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
        this.sectionElements = data.mod_perform_section_admin.section_elements.map(
          item => {
            return {
              id: item.id,
              clientId: uniqueId(),
              element: {
                id: item.element.id,
                type: item.element.element_plugin,
                title: item.element.title,
                raw_title: item.element.raw_title,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
                raw_data: JSON.parse(item.element.raw_data),
              },
              sort_order: item.sort_order,
            };
          }
        );
      },
    },
  },

  methods: {
    /**
     * Add new plugin element
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
          identifier: null,
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
     * update existing elements and shows display view of the element
     */
    update(sectionElement, { title, data }) {
      sectionElement.element.title = title;
      sectionElement.element.raw_title = title;
      sectionElement.element.data = data;
      sectionElement.element.raw_data = data;
      delete sectionElement.creating;
      this.display(sectionElement);
    },

    /**
     * Add element into edit list
     */
    edit(sectionElement) {
      this.editingIds.push(sectionElement.clientId);
    },

    /**
     * Display section element
     * Remove creating view if section element move to display mode
     */
    display(sectionElement) {
      pull(this.editingIds, sectionElement.clientId);
      if (sectionElement.creating) {
        this.remove(sectionElement);
      }
    },

    /**
     * Remove section element
     * if section element already saved update remove list
     */
    remove(sectionElement) {
      pull(this.sectionElements, sectionElement);
      if (sectionElement.element.id) {
        this.removeIds.push({
          section_element_id: sectionElement.element.id,
        });
      }
    },

    /**
     * check element is editing
     */
    isEditing(sectionElement) {
      return this.editingIds.includes(sectionElement.clientId);
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
     * Close the modal if cancel
     */
    cancel() {
      this.$emit('request-close');
    },

    /**
     * Try to persist the activity elements to the back end
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave() {
      this.isSaving = true;

      try {
        await this.save();
        this.$emit('mutation-success');
        this.isSaving = false;
        this.$emit('request-close');
      } catch (e) {
        this.$emit('mutation-error', e);
        // If something goes wrong during create, allow the user to try again.
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
        createNew = [],
        createLink = [],
        update = [],
        move = [];

      this.sectionElements.forEach(function(item, index) {
        let sortOrder = index + 1;
        if (!item.element.id) {
          createNew.push({
            plugin_name: item.element.type.plugin_name,
            title: item.element.raw_title,
            data: JSON.stringify(item.element.raw_data),
            sort_order: sortOrder,
          });
        } else {
          update.push({
            element_id: item.element.id,
            title: item.element.raw_title,
            data: JSON.stringify(item.element.raw_data),
          });
          move.push({
            section_element_id: item.id,
            sort_order: sortOrder,
          });
        }
      });
      variables = {
        input: {
          section_id: this.sectionId,
          create_new: createNew,
          create_link: createLink,
          update: update,
          delete: this.removeIds,
          move: move,
        },
      };

      const { data: resultData } = await this.$apollo.mutate({
        mutation: updateSectionElementMutation,
        variables: variables,
        refetchAll: false,
      });

      return resultData;
    },

    canSubmit() {
      return (
        (!this.isSaving &&
          Object.keys(this.errors).length === 0 &&
          this.hasElementsToAdd()) ||
        this.hasElementsToRemove()
      );
    },

    /**
     * Runs check if modal has elements to remove.
     */
    hasElementsToRemove() {
      return this.removeIds.length > 0;
    },

    /**
     * Runs check if modal has elements to add.
     */
    hasElementsToAdd() {
      const lastSectionElement = [...this.sectionElements].pop();

      return this.sectionElements.length > 0 && !lastSectionElement.creating;
    },
  },
};
</script>
