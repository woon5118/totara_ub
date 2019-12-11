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
  <HelpPopover class="tui-pathwayManual-lastRatingHelp">
    <span v-if="isForSelf">
      {{ $str('last_rating_given_self_tooltip', 'pathway_manual') }}
    </span>
    <span v-else>
      {{ $str('last_rating_given_other_tooltip', 'pathway_manual') }}
    </span>
  </HelpPopover>
</template>

<script>
import HelpPopover from 'totara_competency/components/HelpPopover';

export default {
  components: { HelpPopover },

  props: {
    isForSelf: {
      required: false,
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-lastRatingHelp {
  font-weight: normal;
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "last_rating_given_other_tooltip",
      "last_rating_given_self_tooltip"
    ]
  }
</lang-strings>
