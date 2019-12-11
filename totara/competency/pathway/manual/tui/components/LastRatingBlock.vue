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
  <span class="tui-pathwayManual-lastRatingBlock">
    <span v-if="hasBeenRated" class="tui-pathwayManual-lastRatingBlock__blocks">
      <span v-if="showValue">{{ scaleValue }}</span>
      <span>{{ date }}</span>
      <span
        v-if="raterName"
        class="tui-pathwayManual-lastRatingBlock__raterName"
      >
        {{ raterName }}
      </span>
    </span>
    <span v-else class="tui-pathwayManual-lastRatingBlock__neverRated">{{
      $str('never_rated', 'pathway_manual')
    }}</span>
  </span>
</template>

<script>
export default {
  props: {
    latestRating: {
      required: false,
      type: Object,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
    showValue: {
      required: false,
      type: Boolean,
      default: true,
    },
  },

  computed: {
    hasBeenRated() {
      return this.latestRating != null;
    },

    scaleValue() {
      if (this.latestRating.scale_value == null) {
        return this.$str('rating_set_to_none', 'pathway_manual');
      }
      return this.latestRating.scale_value.name;
    },

    isToday() {
      let specifiedDate = new Date(this.latestRating.date_iso8601);
      let today = new Date();
      return specifiedDate.setHours(0, 0, 0, 0) === today.setHours(0, 0, 0, 0);
    },

    raterPurged() {
      return this.latestRating.rater == null;
    },

    isCurrentUser() {
      return parseInt(this.latestRating.rater.id) === this.currentUserId;
    },

    raterName() {
      if (this.raterPurged || this.isCurrentUser) {
        return null;
      }

      return this.$str(
        'user_fullname_wrapper',
        'pathway_manual',
        this.latestRating.rater.fullname
      );
    },

    date() {
      if (this.isToday) {
        return this.$str('today');
      }
      return this.latestRating.date;
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-lastRatingBlock {
  @media (min-width: $tui-screen-xs) {
    &__blocks {
      & > span {
        display: block;
      }
    }

    &__neverRated {
      display: none;
    }
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "today"
    ],
    "pathway_manual": [
      "never_rated",
      "rating_set_to_none",
      "user_fullname_wrapper"
    ]
  }
</lang-strings>
