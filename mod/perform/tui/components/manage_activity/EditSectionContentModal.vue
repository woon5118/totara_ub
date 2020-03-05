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
          :name="sectionElement.element.name"
          :type="sectionElement.element.type"
          :error="errors[sectionElement.clientId]"
          @update="update(sectionElement, $event)"
          @edit="edit(sectionElement)"
          @display="display(sectionElement)"
          @remove="remove(sectionElement)"
        />

        <ContentAddElementButton @add-element-item="add" />
        <SubmitCancelGroup @submit="submit" @cancel="cancel" />
      </div>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ContentAddElementButton from 'mod_perform/components/manage_activity/ContentAddElementButton';
import SubmitCancelGroup from 'totara_core/components/buttons/SubmitCancelGroup';
import Button from 'totara_core/components/buttons/Button';
import sectionDetailQuery from 'mod_perform/graphql/section_details';
import { pull, uniqueId } from 'totara_core/util';

export default {
  components: {
    Modal,
    ModalContent,
    ContentAddElementButton,
    SubmitCancelGroup,
    Button,
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
      sectionElements: null,
      editingIds: [],
      errors: {},
    };
  },

  apollo: {
    section: {
      query: sectionDetailQuery,
      variables() {
        return { section_id: this.sectionId };
      },
      update: data => data.mod_perform_section,
      result({ data }) {
        this.sectionElements = data.mod_perform_section.section_elements.map(
          item => {
            return {
              id: item.id,
              clientId: uniqueId(),
              element: {
                id: item.element.id,
                type: item.element.element_type,
                name: item.element.name,
                identifier: item.element.identifier,
                data: JSON.parse(item.element.data),
              },
            };
          }
        );
      },
    },
  },

  methods: {
    add(plugin) {
      const sectionElement = {
        id: this.sectionId,
        clientId: uniqueId(),
        element: {
          id: null,
          type: plugin,
          name: '',
          identifier: null,
          data: {},
        },
        creating: true,
      };

      this.sectionElements.push(sectionElement);
      this.edit(sectionElement);
    },

    update(sectionElement, { name, data }) {
      sectionElement.element.name = name;
      sectionElement.element.data = data;
      delete sectionElement.creating;
      this.display(sectionElement);
    },

    edit(sectionElement) {
      this.editingIds.push(sectionElement.clientId);
    },

    display(sectionElement) {
      pull(this.editingIds, sectionElement.clientId);
      if (sectionElement.creating) {
        this.remove(sectionElement);
      }
    },

    remove(sectionElement) {
      pull(this.sectionElements, sectionElement);
    },

    isEditing(sectionElement) {
      return this.editingIds.includes(sectionElement.clientId);
    },

    componentFor(sectionElement) {
      const { type } = sectionElement.element;
      const isEditing = this.editingIds.includes(sectionElement.clientId);
      return tui.asyncComponent(
        'performelement_' +
          type.plugin_name +
          '/components/' +
          (isEditing ? type.admin_form_component : type.admin_display_component)
      );
    },

    submit() {
      console.log('submit');
      console.log(JSON.parse(JSON.stringify(this.sectionElements)));
    },

    cancel() {
      this.$emit('request-close');
    },
  },
};
</script>
