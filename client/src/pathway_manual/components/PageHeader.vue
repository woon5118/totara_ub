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
  <div class="tui-bulkManualRatingPageHeader">
    <div v-if="isForAnotherUser" class="tui-bulkManualRatingPageHeader__title">
      <Avatar :src="user.profileimageurl" :alt="user.fullname" size="medium" />
    </div>
    <h2
      v-if="isForAnotherUser"
      class="tui-bulkManualRatingPageHeader__title tui-bulkManualRatingPageHeader__title-withPhoto"
    >
      {{ $str('rate_user', 'pathway_manual', user.fullname) }}
    </h2>
    <h2 v-else class="tui-bulkManualRatingPageHeader__title">
      {{ $str('rate_competencies', 'pathway_manual') }}
    </h2>
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';

export default {
  components: {
    Avatar,
  },

  props: {
    user: {
      required: true,
      type: Object,
    },
    isForAnotherUser: {
      required: true,
      type: Boolean,
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "rate_competencies",
      "rate_user"
    ]
  }
</lang-strings>
