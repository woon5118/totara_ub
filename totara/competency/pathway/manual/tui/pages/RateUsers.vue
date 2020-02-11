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
    <h2>
      {{ $str('rate_competencies', 'pathway_manual') }}
    </h2>
    <RoleSelector
      :specified-role="specifiedRole"
      :has-default-selected="true"
      @role-selected="roleSelected"
    />
    <RateUsersList v-if="role" :role="role" :current-user-id="currentUserId" />
  </div>
</template>

<script>
import RateUsersList from 'pathway_manual/components/RateUsersList';
import RoleSelector from 'pathway_manual/components/RoleSelector';

export default {
  components: { RateUsersList, RoleSelector },

  props: {
    specifiedRole: {
      type: String,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      role: this.specifiedRole,
    };
  },

  methods: {
    roleSelected(role) {
      this.role = role;
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "rate_competencies"
    ]
  }
</lang-strings>
