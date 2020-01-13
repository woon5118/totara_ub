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
  <div v-if="!$apollo.loading">
    <div class="tui-pathwayManual-rateUser">
      <div v-if="isForSelf">
        <h2 class="tui-pathwayManual-rateUser__title">
          {{ $str('rate_competencies', 'pathway_manual') }}
        </h2>
      </div>
      <div
        v-else
        class="tui-pathwayManual-rateUser tui-pathwayManual-rateUser--withPhoto"
      >
        <UserHeaderWithPhoto
          :page-title="$str('rate_user', 'pathway_manual', user.fullname)"
          :full-name="user.fullname"
          :photo-url="user.profileimageurl"
        />
      </div>
      <div
        v-if="roleSpecified && !isForSelf"
        class="tui-pathwayManual-rateUser__ratingAsRole"
      >
        {{ $str(`rating_as_${roleSpecified}`, 'pathway_manual') }}
      </div>
      <div
        v-else-if="!isForSelf"
        class="tui-pathwayManual-rateUser__ratingAsRole"
      >
        <span>{{ $str('rating_as_a', 'pathway_manual') }}</span>
        <span class="tui-pathwayManual-rateUser__selectRole">
          <Select
            :id="$id('select-role')"
            :options="roleOptions"
            :value="-1"
            @input="selectRoleWithWarning"
          />
        </span>
        <ConfirmModal
          :open="showSelectRoleModal"
          :title="$str('modal:confirm_update_role_title', 'pathway_manual')"
          @confirm="selectRole"
          @cancel="showSelectRoleModal = false"
        >
          <span
            v-html="$str('modal:confirm_update_role_body', 'pathway_manual')"
          />
        </ConfirmModal>
      </div>
    </div>
    <RateCompetencies
      v-if="role"
      :user="user"
      :role="role"
      :current-user-id="currentUserId"
      :assignment-id="assignmentId"
      :go-back-link="goBackLink"
      @has-unsaved-ratings="has => (hasUnsavedRatings = has)"
    />
  </div>
</template>

<script>
import ConfirmModal from 'pathway_manual/components/ConfirmModal';
import RateCompetencies from 'pathway_manual/components/RateCompetencies';
import Select from 'totara_core/components/form/Select';
import UserHeaderWithPhoto from 'pathway_manual/components/UserHeaderWithPhoto';

import RolesQuery from '../../webapi/ajax/roles.graphql';

export default {
  components: {
    ConfirmModal,
    RateCompetencies,
    Select,
    UserHeaderWithPhoto,
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
    assignmentId: {
      required: false,
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      roles: [],
      confirmedRole: null,
      selectedRole: this.specifiedRole,
      showSelectRoleModal: false,
      hasUnsavedRatings: false,
    };
  },

  computed: {
    isForSelf() {
      return this.user.id === this.currentUserId;
    },

    usersRoles() {
      return this.roles.filter(role => role.has_role);
    },

    roleSpecified() {
      if (this.specifiedRole != null) {
        return this.specifiedRole;
      }

      if (this.usersRoles.length === 1) {
        return this.usersRoles[0].name;
      }

      return null;
    },

    role() {
      if (this.confirmedRole != null) {
        return this.confirmedRole;
      }
      return this.roleSpecified;
    },

    roleOptions() {
      return [
        {
          id: -1,
          label: this.$str('select...', 'pathway_manual'),
          disabled: true,
        },
      ].concat(
        this.usersRoles.map(role => {
          return {
            id: role.name,
            label: role.display_name,
          };
        })
      );
    },
  },

  methods: {
    selectRoleWithWarning(role) {
      this.selectedRole = role;

      if (this.confirmedRole != null && this.hasUnsavedRatings) {
        this.showSelectRoleModal = true;
      } else {
        this.selectRole();
      }
    },

    selectRole() {
      this.showSelectRoleModal = false;
      this.confirmedRole = this.selectedRole;
      this.hasUnsavedRatings = false;
    },
  },

  apollo: {
    roles: {
      query: RolesQuery,
      variables() {
        return {
          subject_user: this.user.id,
        };
      },
      update({ pathway_manual_roles: roles }) {
        return roles;
      },
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-rateUser {
  margin-top: var(--tui-gap-1);
  margin-bottom: var(--tui-gap-4);

  &__title {
    margin: 0;
  }

  &__ratingAsRole {
    margin-top: var(--tui-gap-4);
    font-weight: bold;
    font-size: var(--tui-font-size-16);
  }

  &--withPhoto {
    margin-top: var(--tui-gap-2);
  }

  &__selectRole {
    display: inline-block;
    max-width: fit-content;
    margin-left: var(--tui-gap-1);
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "modal:confirm_update_role_body",
      "modal:confirm_update_role_title",
      "rate_competencies",
      "rate_user",
      "rating_as_a",
      "rating_as_appraiser",
      "rating_as_manager",
      "select..."
    ]
  }
</lang-strings>
