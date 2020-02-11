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
  <InfoIconButton
    :aria-label="
      $str(
        'show_help_for_x',
        'totara_competency',
        $str('last_rating_given', 'pathway_manual')
      )
    "
  >
    <template v-if="isForSelf">
      {{ $str('last_rating_given_self_tooltip', 'pathway_manual') }}
    </template>
    <template v-else>
      {{ $str('last_rating_given_other_tooltip', 'pathway_manual') }}
    </template>
  </InfoIconButton>
</template>

<script>
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';

export default {
  components: { InfoIconButton },

  props: {
    isForSelf: {
      required: false,
      type: Boolean,
      default: false,
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "last_rating_given",
      "last_rating_given_other_tooltip",
      "last_rating_given_self_tooltip"
    ],
    "totara_competency": [
      "show_help_for_x"
    ]
  }
</lang-strings>
