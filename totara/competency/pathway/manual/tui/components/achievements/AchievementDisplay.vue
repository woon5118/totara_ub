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
  <div v-if="ratingsEnabled">
    <AchievementDisplayHeader
      :title="$str('raters', 'pathway_manual')"
      :help-text="$str('raters_info', 'pathway_manual')"
    />
    <div class="tui-pathwayManual-achievementDisplay__list">
      <div
        v-for="(roleRating, index) in roleRatings"
        :key="index"
        class="tui-pathwayManual-achievementDisplay__row"
      >
        <div class="tui-pathwayManual-achievementDisplay__role">
          <img
            :src="getProfilePhotoUrl(roleRating)"
            :alt="getUserName(roleRating)"
            :class="getPhotoClass(roleRating)"
          />
          <div class="tui-pathwayManual-achievementDisplay__role_info">
            <div class="tui-pathwayManual-achievementDisplay__role_info_name">
              {{ roleRating.role_display_name }}
            </div>
            <a
              v-if="roleRating.role.has_role"
              :href="getAddRatingUrl(roleRating)"
              >{{ $str('add_rating', 'pathway_manual') }}</a
            >
          </div>
        </div>
        <div
          v-if="hasRating(roleRating)"
          class="tui-pathwayManual-achievementDisplay__rating"
        >
          <div class="tui-pathwayManual-achievementDisplay__rating_value">
            <span v-if="roleRating.latest_rating.scale_value">
              {{ roleRating.latest_rating.scale_value.name }}
            </span>
            <span v-else>
              {{ $str('rating_set_to_none', 'pathway_manual') }}
            </span>
          </div>
          <div
            v-if="isSelf(roleRating)"
            class="tui-pathwayManual-achievementDisplay__rating_date"
          >
            {{ roleRating.latest_rating.date }}
          </div>
          <div class="tui-pathwayManual-achievementDisplay__rating_date">
            {{ getNameAndDate(roleRating) }}
          </div>
          <div
            v-if="hasComment(roleRating)"
            class="tui-pathwayManual-achievementDisplay__rating_comment"
          >
            {{
              $str(
                'comment_wrapper',
                'pathway_manual',
                roleRating.latest_rating.comment
              )
            }}
          </div>
        </div>
        <div v-else class="tui-pathwayManual-achievementDisplay__rating-none">
          {{ $str('no_rating_given', 'pathway_manual') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AchievementDisplayHeader from 'totara_competency/components/details/AchievementDisplayHeader';

import RoleRatingsQuery from '../../../webapi/ajax/role_ratings.graphql';

export default {
  components: { AchievementDisplayHeader },

  props: {
    userId: {
      required: true,
      type: Number,
    },
    assignmentId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      roleRatings: [],
      showInfoTooltip: false,
    };
  },

  computed: {
    ratingsEnabled() {
      return this.roleRatings != null && this.roleRatings.length > 0;
    },
  },

  methods: {
    hasRating(roleRating) {
      return roleRating.latest_rating != null;
    },

    raterPurged(roleRating) {
      return roleRating.latest_rating.rater == null;
    },

    hasComment(roleRating) {
      return roleRating.latest_rating.comment != null;
    },

    isSelf(roleRating) {
      return roleRating.role.name === 'self';
    },

    getAddRatingUrl(roleRating) {
      return this.$url('/totara/competency/rate_competencies.php', {
        user_id: this.userId,
        role: roleRating.role.name,
        assignment_id: this.assignmentId,
      });
    },

    getProfilePhotoUrl(roleRating) {
      if (this.hasRating(roleRating) && !this.raterPurged(roleRating)) {
        return roleRating.latest_rating.rater.profileimageurl;
      }
      return roleRating.default_profile_picture;
    },

    getUserName(roleRating) {
      if (!this.hasRating(roleRating)) {
        return this.$str('no_rating_given', 'pathway_manual');
      } else if (this.raterPurged(roleRating)) {
        return this.$str('rater_details_removed', 'pathway_manual');
      }
      return roleRating.latest_rating.rater.fullname;
    },

    getNameAndDate(roleRating) {
      if (this.raterPurged(roleRating)) {
        return this.$str(
          'date_rater_details_removed',
          'pathway_manual',
          roleRating.latest_rating.date
        );
      }
      return this.$str('fullname_date', 'pathway_manual', {
        name: roleRating.latest_rating.rater.fullname,
        date: roleRating.latest_rating.date,
      });
    },

    getPhotoClass(roleRating) {
      let cssClass = 'tui-pathwayManual-achievementDisplay__role_photo';
      if (!this.hasRating(roleRating)) {
        cssClass += ' tui-pathwayManual-achievementDisplay__role_photo-none';
      }
      return cssClass;
    },
  },

  apollo: {
    roleRatings: {
      query: RoleRatingsQuery,
      context: { batch: true },
      variables() {
        return {
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update({ pathway_manual_role_ratings: roleRatings }) {
        this.$emit('loaded');
        return roleRatings;
      },
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-achievementDisplay {
  &__row {
    padding-top: 1em;
    padding-bottom: 1em;
    border-bottom: 1px solid #dde1e5;

    @media (min-width: $tui-screen-sm) {
      display: flex;
    }
  }

  &__role {
    display: flex;
    @media (min-width: $tui-screen-sm) {
      flex-grow: 1;
      max-width: 25%;
    }
    @media (max-width: $tui-screen-sm) {
      padding-bottom: 1em;
    }

    &_info {
      margin-left: 1em;
      &_name {
        font-weight: bold;
      }
    }

    &_photo {
      height: 3em;

      &-none {
        opacity: 0.5;
      }
    }
  }

  &__rating {
    @media (min-width: $tui-screen-sm) {
      max-width: 75%;
    }

    &_value {
      font-weight: bold;
    }

    &_comment {
      padding-top: 0.5em;
    }

    &-none {
      font-style: italic;
    }
  }
}
</style>

<lang-strings>
  {
    "pathway_manual" : [
      "add_rating",
      "comment_wrapper",
      "fullname_date",
      "no_rating_given",
      "rater_details_removed",
      "raters",
      "raters_info",
      "rating_set_to_none",
      "your_rating"
    ]
  }
</lang-strings>
