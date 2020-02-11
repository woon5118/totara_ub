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
  <span>
    <span
      v-if="latestRating != null"
      class="tui-bulkManualRatingLastRatingBlock__blocks"
    >
      <template v-if="showValue">
        <span v-if="latestRating.scale_value == null">
          {{ $str('rating_set_to_none', 'pathway_manual') }}
        </span>
        <span v-else>{{ latestRating.scale_value.name }}</span>
      </template>
      <span v-if="isToday">
        {{ $str('today') }}
      </span>
      <span v-else>
        {{ latestRating.date }}
      </span>
      <span v-if="!isCurrentUser && !raterPurged">{{
        $str(
          'user_fullname_wrapper',
          'pathway_manual',
          latestRating.rater.fullname
        )
      }}</span>
    </span>
    <span v-else class="tui-bulkManualRatingLastRatingBlock__neverRated">{{
      $str('never_rated', 'pathway_manual')
    }}</span>
  </span>
</template>

<script>
export default {
  props: {
    latestRating: {
      type: Object,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
    showValue: {
      type: Boolean,
      default: true,
    },
  },

  computed: {
    /**
     * Is the date of the last rating today?
     * @returns {boolean}
     */
    isToday() {
      let specifiedDate = new Date(this.latestRating.date_iso8601);
      let today = new Date();
      return specifiedDate.setHours(0, 0, 0, 0) === today.setHours(0, 0, 0, 0);
    },

    /**
     * Has the user data been purged for the person who last rated?
     * @returns {boolean}
     */
    raterPurged() {
      return this.latestRating.rater == null;
    },

    /**
     * Was the current user the last person to make a rating?
     * @returns {boolean}
     */
    isCurrentUser() {
      return parseInt(this.latestRating.rater.id) === this.currentUserId;
    },
  },
};
</script>

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
