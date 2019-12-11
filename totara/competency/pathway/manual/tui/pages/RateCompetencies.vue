<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package pathway_manual
-->

<template>
  <div>
    <RateHeader :user="user" :is-for-another-user="isForAnotherUser" />
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
import RateHeader from 'pathway_manual/components/RateHeader';
import RateUserCompetencies from 'pathway_manual/components/RateUserCompetencies';
import RoleSelector from 'pathway_manual/components/RoleSelector';

export default {
  components: {
    RateHeader,
    RateUserCompetencies,
    RoleSelector,
  },

  props: {
    user: {
      required: true,
      type: Object,
    },
    specifiedRole: {
      required: false,
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
    assignmentId: {
      required: false,
      type: Number,
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

<style lang="scss">
.tui-pathwayManual-rateCompetencies {
  margin-bottom: var(--tui-gap-4);

  &__backLink {
    display: inline-block;
    align-self: start;
    padding-bottom: var(--tui-gap-2);
  }
}
</style>

<lang-strings>
  {
    "totara_competency": [
      "back_to_competency_profile"
    ]
  }
</lang-strings>
