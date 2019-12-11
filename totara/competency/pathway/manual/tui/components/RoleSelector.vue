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
  <div v-if="!$apollo.loading" class="tui-pathwayManual-roleSelector">
    <div v-if="roleSpecified">
      {{ $str(`rating_as_${roleSpecified}`, 'pathway_manual') }}
    </div>
    <div v-else>
      <span>{{ $str('rating_as_a', 'pathway_manual') }}</span>
      <span class="tui-pathwayManual-roleSelector__selectRole">
        <Select
          :id="$id('select-role')"
          :options="roleOptions"
          :value="getDefaultValue()"
          @input="selectRoleWithWarning"
        />
      </span>
      <ConfirmModal
        :open="modalVisible"
        :title="$str('modal:confirm_update_role_title', 'pathway_manual')"
        @confirm="selectRole"
        @cancel="hideModal"
      >
        <span
          v-html="$str('modal:confirm_update_role_body', 'pathway_manual')"
        />
      </ConfirmModal>
    </div>
  </div>
</template>

<script>
import ConfirmModal from 'pathway_manual/components/ConfirmModal';
import Select from 'totara_core/components/form/Select';

import RolesQuery from '../../webapi/ajax/roles.graphql';

export const ROLE_SELF = 'self';

const UNSET_OPTION = -1;

export default {
  components: {
    ConfirmModal,
    Select,
  },

  props: {
    userId: {
      required: false,
      type: Number,
    },
    specifiedRole: {
      required: false,
      type: String,
    },
    showWarning: {
      required: false,
      type: Boolean,
      default: false,
    },
    hasDefaultSelected: {
      required: false,
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      roles: [],
      confirmedRole: null,
      selectedRole: this.specifiedRole,
      modalVisible: false,
    };
  },

  computed: {
    roleSpecified() {
      if (this.specifiedRole != null) {
        return this.specifiedRole;
      }

      if (this.roles.length === 1) {
        return this.roles[0].name;
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
      let roles = this.roles.map(role => {
        return {
          id: role.name,
          label: role.display_name,
        };
      });

      if (!this.hasDefaultSelected) {
        roles.unshift({
          id: UNSET_OPTION,
          label: this.$str('select...', 'pathway_manual'),
          disabled: true,
        });
      }

      return roles;
    },
  },

  methods: {
    selectRoleWithWarning(role) {
      this.selectedRole = role;

      if (this.confirmedRole != null && this.showWarning) {
        this.showModal();
      } else {
        this.selectRole();
      }
    },

    selectRole() {
      this.hideModal();
      this.confirmedRole = this.selectedRole;
      this.$emit('role-selected', this.confirmedRole);
    },

    getDefaultValue() {
      if (this.hasDefaultSelected && this.roles.length > 0) {
        this.$emit('role-selected', this.roles[0].name);
        return this.roles[0].name;
      } else {
        return UNSET_OPTION;
      }
    },

    showModal() {
      this.modalVisible = true;
    },

    hideModal() {
      this.modalVisible = false;
    },
  },

  apollo: {
    roles: {
      query: RolesQuery,
      variables() {
        if (this.userId == null) {
          return {};
        }
        return {
          subject_user: this.userId,
        };
      },
      update({ pathway_manual_roles: roles }) {
        if (roles.length === 1) {
          this.$emit('role-selected', roles[0].name);
        }

        return roles;
      },
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-roleSelector {
  @include tui-font-heading-x-small;
  margin-top: var(--tui-gap-4);

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
      "rating_as_a",
      "rating_as_appraiser",
      "rating_as_manager",
      "select..."
    ]
  }
</lang-strings>
