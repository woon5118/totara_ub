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
