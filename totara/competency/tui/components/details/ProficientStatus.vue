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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <span
    class="tui-competencyDetailProficientStatus"
    :class="{
      'tui-competencyDetailProficientStatus-srOnly': !proficientStatus,
    }"
  >
    <ProficientIcon v-if="proficientStatus" size="200" />
    <span>
      {{
        $str(
          proficientStatus ? 'proficient' : 'not_proficient',
          'totara_competency'
        )
      }}
    </span>
  </span>
</template>

<script>
import ProficientIcon from 'totara_core/components/icons/common/CheckSuccess';

export default {
  components: {
    ProficientIcon,
  },
  props: {
    proficientStatus: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "not_proficient",
      "proficient"
    ]
  }
</lang-strings>
