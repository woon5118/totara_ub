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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-performManageActivityContent">
    <WorkflowOptions :activity="value" />

    <h3 class="tui-performManageActivityContent__heading">
      {{ $str('activity_content_tab_heading', 'mod_perform') }}
    </h3>

    <ActivityMultipleSectionToggle
      :activity="value"
      @change="updateMultiSection($event)"
    />
    <div class="tui-performManageActivityContent__items">
      <ActivitySection
        v-for="(sectionState, i) in displayedSectionStates"
        :key="i"
        :auto-save="autoSave"
        :edit-mode="sectionState.editMode"
        :section="sectionState.section"
        @input="updateSection($event, i)"
        @toggle-edit-mode="toggleSectionStateEditMode($event, i)"
        @mutation-success="$emit('mutation-success')"
        @mutation-error="$emit('mutation-error')"
      />
    </div>

    <ButtonIcon
      v-show="value.settings.multisection"
      :aria-label="$str('add_section', 'mod_perform')"
      :text="$str('add_section', 'mod_perform')"
      :styleclass="{ small: true }"
      @click.prevent="addSection"
    >
      <AddIcon size="200" />
    </ButtonIcon>
  </div>
</template>

<script>
import ActivityMultipleSectionToggle from 'mod_perform/components/manage_activity/content/ActivityMultipleSectionToggle';
import ActivitySection from 'mod_perform/components/manage_activity/content/ActivitySection';
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import AddSectionMutation from 'mod_perform/graphql/add_section.graphql';
import WorkflowOptions from 'mod_perform/components/manage_activity/WorkflowOptions';

export default {
  components: {
    ActivityMultipleSectionToggle,
    ActivitySection,
    AddIcon,
    ButtonIcon,
    WorkflowOptions,
  },
  props: {
    value: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      sectionStates: this.createSectionStates(this.value),
    };
  },
  computed: {
    /**
     * Checks if section should auto-save on changes.
     *
     * @return {boolean}
     */
    autoSave() {
      return !this.value.settings.multisection;
    },
    /**
     * Returns section states.
     *
     * @return {Array}
     */
    displayedSectionStates() {
      return this.value.settings.multisection
        ? this.sectionStates
        : [this.sectionStates[0]];
    },
  },
  methods: {
    /**
     * Updates an activity section.
     * @param {Object} updatedSection Section data
     * @param {int} sectionIndex Section index
     */
    updateSection(updatedSection, sectionIndex) {
      const sectionsCopy = this.value.sections.slice();
      sectionsCopy[sectionIndex] = updatedSection;

      const valueCopy = Object.assign({}, this.value, {
        sections: sectionsCopy,
      });

      this.updateActivity(valueCopy);
      this.updateSectionState(updatedSection, sectionIndex);
    },
    /**
     * Changes edit mode for a section state.
     * @param {Boolean} editMode Section data
     * @param {int} i Section index
     */
    toggleSectionStateEditMode(editMode, i) {
      this.sectionStates[i].editMode = editMode;
    },
    /**
     * Update a section state.
     * @param {Object} update Section data
     * @param {int} i Section index
     */
    updateSectionState(update, i) {
      this.sectionStates[i].section = update;
    },
    /**
     * Create section states from sections.
     * @param {Object} activity
     *
     * @return {Array}
     */
    createSectionStates(activity) {
      return activity.sections && activity.sections.length > 0
        ? activity.sections.map(section => {
            return this.createSectionState(section);
          })
        : [];
    },
    /**
     * Create section state for one section.
     * @param {Object} section
     * @param {Boolean} editMode
     *
     * @return {Object}
     */
    createSectionState(section, editMode = false) {
      return {
        editMode: editMode,
        section: section,
      };
    },
    /**
     * Update activity multisection.
     * @param {Object} update
     */
    updateMultiSection(update) {
      this.sectionStates[0].editMode = update.settings.multisection;
      this.updateActivity(update);
    },
    /**
     * Update activity.
     * @param {Object} update
     */
    updateActivity(update) {
      const newValue = Object.assign({}, this.value, update);

      this.$emit('input', newValue);
    },
    /**
     * Save changes.
     */
    async trySave() {
      try {
        const updatedActivity = this.value;
        this.updateActivity(updatedActivity);
        this.$emit('mutation-success');
      } catch (e) {
        this.$emit('mutation-error', e);
      }
    },
    /**
     * Add a new section at the end of the list
     * @return {Promise<void>}
     */
    async addSection() {
      const {
        data: {
          mod_perform_add_section: { section },
        },
      } = await this.$apollo.mutate({
        mutation: AddSectionMutation,
        variables: {
          input: {
            activity_id: this.value.id,
          },
        },
        refetchAll: false,
      });

      // Add newly created section to the list of section
      this.sectionStates.push(this.createSectionState(section, true));

      // Make sure we scroll to the newly created section
      this.$nextTick(() => {
        if (this.$children[this.$children.length - 1]) {
          this.$children[this.$children.length - 1].$el.scrollIntoView();
        }
      });
      return section;
    },
  },
};
</script>

<lang-strings>
  {
  "mod_perform": [
  "activity_content_tab_heading",
  "add_section"
  ]
  }
</lang-strings>
