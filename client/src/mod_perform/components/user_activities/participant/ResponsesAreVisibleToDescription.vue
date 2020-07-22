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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
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
import { RELATIONSHIP_SUBJECT } from 'mod_perform/constants';

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
          relationship.name_plural
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
          relationship.name_plural
        )
      );

      Array.prototype.push.apply(descriptions, otherDescriptions);

      return descriptions;
    },
    visibleToSubject() {
      return this.visibleToRelationships.some(
        relationship => relationship.idnumber === RELATIONSHIP_SUBJECT
      );
    },
    nonSubjectRelationships() {
      return this.visibleToRelationships.filter(
        relationship => relationship.idnumber !== RELATIONSHIP_SUBJECT
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
