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

  @author Murali Nair <murali.nair@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-performManualParticipantSelectionBanner">
    <ActionCard>
      <template v-slot:card-body>
        <span>{{
          $str(
            'user_activities_require_manual_participant_selection_text',
            'mod_perform'
          )
        }}</span>
      </template>
      <template v-slot:card-action>
        <ActionLink
          :href="$url('/mod/perform/activity/select-participants.php')"
          :text="
            $str(
              'user_activities_require_manual_participant_selection_link',
              'mod_perform'
            )
          "
          :styleclass="{
            primary: true,
          }"
        />
      </template>
    </ActionCard>
  </div>
</template>

<script>
import ActionCard from 'tui/components/card/ActionCard';
import ActionLink from 'tui/components/links/ActionLink';

export default {
  components: {
    ActionCard,
    ActionLink,
  },

  props: {},

  data() {
    return {};
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "user_activities_require_manual_participant_selection_text",
      "user_activities_require_manual_participant_selection_link"
    ]
  }
</lang-strings>
