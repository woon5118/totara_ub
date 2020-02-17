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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @package totara_competency
-->

<template>
  <div v-if="display" class="tui-noCompetencyAssignments">
    <div class="alert alert-info alert-with-icon">
      <!-- TODO bootstrap alert -->
      <div class="alert-icon">
        <FlexIcon icon="notification-info" />
      </div>
      <div
        class="alert-message"
        v-text="$str('no_competencies_assigned', 'totara_competency')"
      />
    </div>
    <div class="tui-noCompetencyAssignments__search-competencies">
      <ActionLink
        :href="selfAssignmentUrl"
        :text="
          isMine
            ? $str('self_assign_competencies', 'totara_competency')
            : $str('assign_competencies', 'totara_competency')
        "
        :styleclass="{ primary: true }"
      />
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import ActionLink from 'totara_core/components/links/ActionLink';

export default {
  components: { FlexIcon, ActionLink },

  props: {
    display: {
      type: Boolean,
      default: true,
    },

    selfAssignmentUrl: {
      required: true,
      type: String,
    },

    isMine: Boolean,
  },
};
</script>

<lang-strings>
{
  "totara_competency": [
    "assign_competencies",
    "self_assign_competencies",
    "no_competencies_assigned"
  ]
}
</lang-strings>
