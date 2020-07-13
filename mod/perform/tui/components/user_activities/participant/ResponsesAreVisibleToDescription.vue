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
  <p>
    <template v-if="notVisibleToAnyone">
      {{ $str('response_visibility_not_visible_to_anyone', 'mod_perform') }}
    </template>
    <template v-else>
      <span
        v-if="anonymousResponses"
        v-html="$str('response_visibility_label_anonymous', 'mod_perform')"
      />
      <span v-else v-html="$str('response_visibility_label', 'mod_perform')" />

      <template v-for="(description, i) in relationshipDescriptions">
        <span :key="i" v-html="description" /><template
          v-if="i + 1 !== relationshipDescriptions.length"
          >,
        </template>
      </template>
    </template>
  </p>
</template>

<script>
export default {
  props: {
    currentUserIsSubject: {
      type: Boolean,
      required: true,
    },
    visibleToRelationships: {
      type: Array,
      required: true,
    },
    anonymousResponses: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    notVisibleToAnyone() {
      return (
        this.visibleToRelationships.length === 0 ||
        (this.currentUserIsSubject && this.visibleToSubjectOnly)
      );
    },
    visibleToSubjectOnly() {
      return this.visibleToRelationships.length === 1 && this.visibleToSubject;
    },
    relationshipDescriptions() {
      if (this.currentUserIsSubject) {
        return this.descriptionsForSubject;
      }

      return this.descriptionsForNonSubject;
    },
    descriptionsForSubject() {
      return this.nonSubjectRelationships.map(relationship =>
        this.$str(
          'response_visibility_your_relationship',
          'mod_perform',
          relationship.core_relationship.name_plural
        )
      );
    },
    descriptionsForNonSubject() {
      const descriptions = [];

      if (this.visibleToSubject) {
        descriptions.push(
          this.$str('response_visibility_the_employee', 'mod_perform')
        );
      }

      const otherDescriptions = this.nonSubjectRelationships.map(relationship =>
        this.$str(
          'response_visibility_the_employees_relationship',
          'mod_perform',
          relationship.core_relationship.name_plural
        )
      );

      Array.prototype.push.apply(descriptions, otherDescriptions);

      return descriptions;
    },
    visibleToSubject() {
      return this.visibleToRelationships.some(
        relationship => relationship.is_subject
      );
    },
    nonSubjectRelationships() {
      return this.visibleToRelationships.filter(
        relationship => !relationship.is_subject
      );
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "response_visibility_label",
      "response_visibility_label_anonymous",
      "response_visibility_not_visible_to_anyone",
      "response_visibility_the_employee",
      "response_visibility_the_employees_relationship",
      "response_visibility_your_relationship"
    ]
  }
</lang-strings>
