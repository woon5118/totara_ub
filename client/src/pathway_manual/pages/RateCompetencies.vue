<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module pathway_manual
-->

<template>
  <div>
    <PageHeader :user="user" :is-for-another-user="isForAnotherUser" />
    <RoleSelector
      v-if="isForAnotherUser"
      :user-id="user.id"
      :specified-role="specifiedRole"
      :show-warning="hasUnsavedRatings"
      @role-selected="roleSelected"
    />
    <RateUserCompetencies
      v-if="role"
      :user="user"
      :role="role"
      :current-user-id="currentUserId"
      :assignment-id="assignmentId"
      @has-unsaved-ratings="has => (hasUnsavedRatings = has)"
      @go-back="goBack"
    />
  </div>
</template>

<script>
import PageHeader from 'pathway_manual/components/PageHeader';
import RateUserCompetencies from 'pathway_manual/components/RateUserCompetencies';
import RoleSelector from 'pathway_manual/components/RoleSelector';

export default {
  components: {
    PageHeader,
    RateUserCompetencies,
    RoleSelector,
  },

  props: {
    user: {
      required: true,
      type: Object,
    },
    specifiedRole: {
      type: String,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
    returnUrl: {
      required: true,
      type: String,
    },
    assignment: {
      type: Object,
    },
  },

  data() {
    return {
      role: this.specifiedRole,
      hasUnsavedRatings: false,
    };
  },

  computed: {
    hasRole() {
      return this.role != null;
    },

    isForAnotherUser() {
      return this.user.id !== this.currentUserId;
    },

    assignmentId() {
      if (this.assignment) {
        return parseInt(this.assignment.assignment_id);
      }
      return null;
    },
  },

  methods: {
    roleSelected(selectedRole) {
      this.hasUnsavedRatings = false;
      this.role = selectedRole;
    },

    goBack() {
      window.location = this.returnUrl;
    },
  },
};
</script>
