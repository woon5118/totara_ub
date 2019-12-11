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
    <div v-if="isForAnotherUser" class="tui-pathwayManual-rateHeader">
      <div class="tui-pathwayManual-rateHeader">
        <Avatar
          :src="user.profileimageurl"
          :alt="user.fullname"
          size="medium"
        />
        <h2
          class="tui-pathwayManual-rateHeader__title tui-pathwayManual-rateHeader__title--withPhoto"
        >
          {{ $str('rate_user', 'pathway_manual', user.fullname) }}
        </h2>
      </div>
    </div>
    <div v-else class="tui-pathwayManual-rateHeader">
      <h2 class="tui-pathwayManual-rateHeader__title">
        {{ $str('rate_competencies', 'pathway_manual') }}
      </h2>
    </div>
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';

export default {
  components: { Avatar },

  props: {
    user: {
      required: false,
      type: Object,
    },
    isForAnotherUser: {
      required: false,
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-rateHeader {
  display: table-row;

  &__photoContainer,
  &__title {
    display: table-cell;
  }

  &__title {
    margin: 0;
    vertical-align: middle;
    &--withPhoto {
      padding-left: var(--tui-gap-3);
    }
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "rate_competencies",
      "rate_user"
    ]
  }
</lang-strings>
