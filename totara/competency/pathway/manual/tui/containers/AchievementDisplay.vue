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
        v-for="(role, index) in roles"
        :key="index"
        class="tui-pathwayManual-achievementDisplay__row"
      >
        <div class="tui-pathwayManual-achievementDisplay__role">
          <img
            :src="getProfilePhotoUrl(role)"
            :alt="getUserName(role)"
            :class="getPhotoClass(role)"
          />
          <div class="tui-pathwayManual-achievementDisplay__role_info">
            <div class="tui-pathwayManual-achievementDisplay__role_info_name">
              {{ role.role_display_name }}
            </div>
            <a v-if="role.has_role" :href="getAddRatingUrl(role)">{{
              $str('add_rating', 'pathway_manual')
            }}</a>
          </div>
        </div>
        <div
          v-if="hasRating(role)"
          class="tui-pathwayManual-achievementDisplay__rating"
        >
          <div class="tui-pathwayManual-achievementDisplay__rating_value">
            {{ role.latest_rating.scale_value.name }}
          </div>
          <div
            v-if="isSelf(role)"
            class="tui-pathwayManual-achievementDisplay__rating_date"
          >
            {{ role.latest_rating.date }}
          </div>
          <div v-else class="tui-pathwayManual-achievementDisplay__rating_date">
            {{
              $str('fullname_date', 'pathway_manual', {
                name: role.latest_rating.rater.fullname,
                date: role.latest_rating.date,
              })
            }}
          </div>
          <div
            v-if="hasComment(role)"
            class="tui-pathwayManual-achievementDisplay__rating_comment"
          >
            {{
              $str(
                'comment_wrapper',
                'pathway_manual',
                role.latest_rating.comment
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
import AchievementDisplayHeader from 'totara_competency/presentation/Details/AchievementDisplayHeader';

import RoleRatingsQuery from '../../webapi/ajax/role_ratings.graphql';

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
      roles: [],
      showInfoTooltip: false,
    };
  },

  computed: {
    ratingsEnabled() {
      return this.roles != null && this.roles.length > 0;
    },
  },

  methods: {
    hasRating(role) {
      return role.latest_rating != null;
    },

    hasComment(role) {
      return role.latest_rating.comment != null;
    },

    isSelf(rating) {
      return rating.role === 'self';
    },

    getAddRatingUrl(role) {
      // TODO: Return url for making manual ratings in TL-21734
      return '#' + role.role;
    },

    getProfilePhotoUrl(role) {
      if (this.hasRating(role)) {
        return role.latest_rating.rater.profileimageurl;
      }
      return role.default_profile_picture;
    },

    getUserName(role) {
      if (this.hasRating(role)) {
        return role.latest_rating.rater.fullname;
      }
      return this.$str('no_rating_given', 'pathway_manual');
    },

    getPhotoClass(role) {
      let cssClass = 'tui-pathwayManual-achievementDisplay__role_photo';
      if (!this.hasRating(role)) {
        cssClass += ' tui-pathwayManual-achievementDisplay__role_photo-none';
      }
      return cssClass;
    },
  },

  apollo: {
    roles: {
      query: RoleRatingsQuery,
      context: { batch: true },
      variables() {
        return {
          user_id: this.userId,
          assignment_id: this.assignmentId,
        };
      },
      update: roles => roles.pathway_manual_role_ratings,
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-achievementDisplay {
  &__row {
    border-bottom: 1px solid #dde1e5;
    padding-top: 1em;
    padding-bottom: 1em;

    @media (min-width: $totara_style-screen_sm_min) {
      display: flex;
    }
  }

  &__role {
    display: flex;
    @media (min-width: $totara_style-screen_sm_min) {
      flex-grow: 1;
      max-width: 25%;
    }
    @media (max-width: $totara_style-screen_sm_min) {
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
    @media (min-width: $totara_style-screen_sm_min) {
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
      "raters",
      "raters_info"
    ]
  }
</lang-strings>
