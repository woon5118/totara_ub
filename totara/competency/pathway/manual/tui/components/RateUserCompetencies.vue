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
    <div
      v-if="isForSelf"
      class="tui-pathwayManual-rateUserCompetencies__header"
    >
      <h2 class="tui-pathwayManual-rateUserCompetencies__header_title">
        {{ $str('rate_competencies', 'pathway_manual') }}
      </h2>
    </div>
    <div
      v-else
      class="tui-pathwayManual-rateUserCompetencies__header tui-pathwayManual-rateUserCompetencies__header--withPhoto"
    >
      <UserHeaderWithPhoto
        :page-title="$str('rate_user', 'pathway_manual', data.user.fullname)"
        :full-name="data.user.fullname"
        :photo-url="data.user.profileimageurl"
      />
      <div class="tui-pathwayManual-rateUserCompetencies__header_ratingAsRole">
        {{ $str(`rating_as_${role}`, 'pathway_manual') }}
      </div>
    </div>
    <div v-if="hasRateableCompetencies">
      <div class="tui-pathwayManual-rateUserCompetencies__filters">
        <div
          class="tui-pathwayManual-rateUserCompetencies__filters_competencyCount"
        >
          <strong>{{
            $str('number_of_competencies', 'pathway_manual', data.count)
          }}</strong>
        </div>
      </div>
      <ScaleTable
        v-for="(scale, index) in data.scales"
        :key="index"
        :scale="scale"
      />
    </div>
    <div v-else>
      <em>{{ $str('no_rateable_competencies', 'pathway_manual') }}</em>
    </div>
  </div>
</template>

<script>
import ScaleTable from 'pathway_manual/containers/ScaleTable';
import UserHeaderWithPhoto from 'pathway_manual/presentation/UserHeaderWithPhoto';

import RateableCompetenciesQuery from '../../webapi/ajax/rateable_competencies.graphql';

const ROLE_SELF = 'self';

export default {
  components: { UserHeaderWithPhoto, ScaleTable },

  props: {
    userId: {
      required: true,
      type: Number,
    },
    role: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      data: {},
    };
  },

  computed: {
    hasRateableCompetencies() {
      return this.data.count > 0;
    },

    isForSelf() {
      return this.role === ROLE_SELF;
    },
  },

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
.tui-pathwayManual-rateUserCompetencies {
  &__header {
    margin-top: var(--tui-gap-1);
    margin-bottom: var(--tui-gap-4);

    &_title {
      margin: 0;
    }

    &_ratingAsRole {
      margin-top: var(--tui-gap-4);
      font-weight: bold;
      font-size: var(--tui-font-size-16);
    }

    &--withPhoto {
      margin-top: var(--tui-gap-2);
    }
  }

  &__filters {
    &_competencyCount {
      width: 100%;
      margin-bottom: var(--tui-gap-4);
      padding: var(--tui-gap-1) var(--tui-gap-2);
      background-color: var(--tui-color-neutral-4);
    }
  }

  &__scaleTable {
    &:not(:last-child) {
      margin-bottom: var(--tui-gap-7);
    }
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "no_rateable_competencies",
      "number_of_competencies",
      "rate_user",
      "rating_as_appraiser",
      "rating_as_manager"
    ]
  }
</lang-strings>
