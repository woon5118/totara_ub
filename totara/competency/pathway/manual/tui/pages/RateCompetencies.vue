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
  <div v-if="!$apollo.loading">
    <a :href="backLinkUrl" class="tui-pathwayManual-rateCompetencies__backLink">
      {{ $str('back_to_competency_profile', 'totara_competency') }}
    </a>
    <h2>{{ $str('rate_competencies', 'pathway_manual') }}</h2>
    <div v-if="hasRateableCompetencies">
      <p v-if="isForOther">
        <strong>{{ $str(`rating_as_${role}`, 'pathway_manual') }}</strong>
      </p>
      <p>
        Total Competencies than can be rated: <strong>{{ data.count }}</strong>
      </p>
      <p>It would be a good idea to put some tables here!</p>
    </div>
    <div v-else class="tui-pathwayManual-rateCompetencies__noCompetencies">
      {{ $str('no_rateable_competencies', 'pathway_manual') }}
    </div>
  </div>
</template>

<script>
import RateableCompetenciesQuery from '../../webapi/ajax/rateable_competencies.graphql';

const ROLE_SELF = 'self';

export default {
  components: {},

  props: {
    userId: {
      type: Number,
    },
    role: {
      type: String,
    },
  },

  data() {
    return {
      data: {},
    };
  },

  computed: {
    backLinkUrl() {
      return this.$url('/totara/competency/profile/', {
        user_id: this.userId,
      });
    },

    hasRateableCompetencies() {
      return this.data.count > 0;
    },

    isForOther() {
      return this.role !== ROLE_SELF;
    },
  },

  mounted() {},

  methods: {},

  apollo: {
    data: {
      query: RateableCompetenciesQuery,
      variables() {
        return {
          user_id: this.userId,
          role: this.role,
        };
      },
      update({ pathway_manual_rateable_competencies: data }) {
        return data;
      },
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
  &__noCompetencies {
    font-style: italic;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "no_rateable_competencies",
      "rate_competencies",
      "rating_as_appraiser",
      "rating_as_manager"
    ],
    "totara_competency": [
      "back_to_competency_profile"
    ]
  }
</lang-strings>
