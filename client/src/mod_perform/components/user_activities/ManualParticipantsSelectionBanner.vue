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
import ActionCard from 'totara_core/components/card/ActionCard';
import ActionLink from 'totara_core/components/links/ActionLink';

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
