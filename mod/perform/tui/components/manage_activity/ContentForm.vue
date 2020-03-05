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
  @package totara_perform
-->

<template>
  <div>
    <h3>{{ $str('activity_content_tab:heading', 'mod_perform') }}</h3>

    <ActivitySection
      v-for="(section, i) in value.sections"
      :key="i"
      :section="section"
      @input="updateSection($event, i)"
      @mutation-success="$emit('mutation-success')"
      @mutation-error="$emit('mutation-error')"
    />
  </div>
</template>

<script>
import ActivitySection from 'mod_perform/components/manage_activity/ActivitySection';
import UpdateGeneralInfoMutation from '../../../webapi/ajax/update_activity_general_info.graphql';

export default {
  components: {
    ActivitySection: ActivitySection,
  },
  props: {
    value: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isSaving: false,
    };
  },
  methods: {
    updateSection(updatedSection, sectionIndex) {
      const sectionsCopy = this.value.sections.slice();
      sectionsCopy[sectionIndex] = updatedSection;

      const valueCopy = Object.assign({}, this.value, {
        sections: sectionsCopy,
      });

      this.updateActivity(valueCopy);
    },
    updateActivity(update) {
      const newValue = Object.assign({}, this.value, update);

      this.$emit('input', newValue);
    },
    async trySave() {
      this.isSaving = true;

      try {
        const updatedActivity = this.value;
        // const updatedActivity = await this.$_save();
        this.updateActivity(updatedActivity);
        this.$emit('mutation-success');
      } catch (e) {
        this.$emit('mutation-error', e);
      } finally {
        this.isSaving = false;
      }
    },
    async $_save() {
      const {
        data: {
          mod_perform_update_activity_general_info: { activity },
        },
      } = await this.$apollo.mutate({
        mutation: UpdateGeneralInfoMutation, // TODO change to the appropriate mutation/varibles
        variables: {
          activity_id: this.value.id,
          name: this.value.name,
          description: this.value.description,
        },
        refetchAll: false, // Don't refetch all the data again
      });

      return activity;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "general_info_label:activity_description",
      "activity_content_tab:heading",
      "save_changes"
    ]
  }
</lang-strings>
