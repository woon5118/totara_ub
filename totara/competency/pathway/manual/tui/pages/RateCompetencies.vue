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
    <a :href="backLinkUrl" class="tui-pathwayManual-rateCompetencies__backLink">
      {{ $str('back_to_competency_profile', 'totara_competency') }}
    </a>
    <RateUserCompetencies
      v-if="userId"
      :user-id="userId"
      :role="role"
      :current-user-id="currentUserId"
      :go-back-link="goBackLink"
    />
  </div>
</template>

<script>
import RateUserCompetencies from 'pathway_manual/containers/RateUserCompetencies';

export default {
  components: { RateUserCompetencies },

  props: {
    userId: {
      type: Number,
    },
    role: {
      type: String,
    },
    currentUserId: {
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
  },

  computed: {
    backLinkUrl() {
      return this.$url('/totara/competency/profile/', {
        user_id: this.userId,
      });
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-rateCompetencies {
  &__backLink {
    display: inline-block;
    align-self: start;
    padding-bottom: var(--tui-gap-1);
  }
}
</style>

<lang-strings>
  {
    "totara_competency": [
      "back_to_competency_profile"
    ]
  }
</lang-strings>
