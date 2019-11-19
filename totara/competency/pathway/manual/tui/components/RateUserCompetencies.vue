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
  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package pathway_manual
-->

<template>
  <div v-if="isLoaded">
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
    <div>
      <div class="tui-pathwayManual-rateUserCompetencies__filters">
        <UserCompetenciesFilters
          v-if="filterOptions"
          :filter-options="filterOptions"
          :has-ratings="hasSelectedRatings"
          @update-filters="applyFilters"
        />
      </div>
      <div v-if="hasRateableCompetencies">
        <FrameworkGroup
          v-for="group in data.framework_groups"
          :key="group.framework.id"
          :group="group"
          :role="role"
          :current-user-id="currentUserId"
          :expanded="expandFrameworkGroups"
          @input="updateRatings"
        />
        <div class="tui-pathwayManual-rateUserCompetencies__submitButtons">
          <ButtonGroup>
            <Button
              :styleclass="{ primary: 'true' }"
              :text="$str('submit')"
              :disabled="!hasSelectedRatings"
              type="submit"
              @click="showSubmitRatingsModal = true"
            />
            <Button :text="$str('cancel')" @click="formCancel" />
          </ButtonGroup>
          <ConfirmModal
            :open="showSubmitRatingsModal"
            :title="
              $str('modal:submit_ratings_confirmation_title', 'pathway_manual')
            "
            @confirm="submitRatings"
            @cancel="showSubmitRatingsModal = false"
          >
            <p>{{ submitRatingsModalMessage }}</p>
            <p>
              <strong>{{
                $str(
                  'modal:submit_ratings_confirmation_question',
                  'pathway_manual'
                )
              }}</strong>
            </p>
          </ConfirmModal>
        </div>
      </div>
      <div v-else>
        <em>{{ $str('filter:no_competencies', 'pathway_manual') }}</em>
      </div>
    </div>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ConfirmModal from 'pathway_manual/components/ConfirmModal';
import FrameworkGroup from 'pathway_manual/components/FrameworkGroup';
import UserCompetenciesFilters from 'pathway_manual/components/UserCompetenciesFilters';
import UserHeaderWithPhoto from 'pathway_manual/components/UserHeaderWithPhoto';

import CreateManualRatingsMutation from '../../webapi/ajax/create_manual_ratings.graphql';
import RateableCompetenciesQuery from '../../webapi/ajax/user_rateable_competencies.graphql';

const ROLE_SELF = 'self';

/**
 * If there are more than this amount of competencies available to rate,
 * then the framework groups should be collapsed by default for performance reasons.
 *
 * @type {number}
 */
const MAX_COMPETENCIES_TO_DISPLAY = 50; // TODO: Should do some performance testing to pick an appropriate number

export default {
  components: {
    Button,
    ButtonGroup,
    ConfirmModal,
    FrameworkGroup,
    UserCompetenciesFilters,
    UserHeaderWithPhoto,
  },

  props: {
    userId: {
      required: true,
      type: Number,
    },
    role: {
      required: true,
      type: String,
    },
    currentUserId: {
      required: true,
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      data: {},
      filterOptions: {},
      showSubmitRatingsModal: false,
      selectedRatings: [],
    };
  },

  computed: {
    isLoaded() {
      return this.data.user != null;
    },

    hasRateableCompetencies() {
      return this.data.framework_groups.length > 0;
    },

    isForSelf() {
      return this.role === ROLE_SELF;
    },

    expandFrameworkGroups() {
      return this.data.count < MAX_COMPETENCIES_TO_DISPLAY;
    },

    hasSelectedRatings() {
      return this.selectedRatings.length > 0;
    },

    numSelectedRatings() {
      return this.selectedRatings.length;
    },

    submitRatingsModalMessage() {
      let ratingSummary = 'modal:submit_ratings_summary';
      ratingSummary += this.numSelectedRatings === 1 ? '_singular' : '_plural';
      ratingSummary += this.isForSelf ? '_self' : '_other';

      let confirmMsgParams = {
        amount: this.numSelectedRatings,
        subject_user: this.data.user.fullname,
      };

      return this.$str(ratingSummary, 'pathway_manual', confirmMsgParams);
    },
  },

  methods: {
    applyFilters(filters) {
      this.selectedRatings = [];
      this.$apollo.queries.data.refetch({
        user_id: this.userId,
        role: this.role,
        filters: filters,
      });
    },

    formCancel() {
      window.location.href = this.goBackLink;
    },

    updateRatings(rating) {
      // Remove rating for this competency from array if it already exists.
      this.selectedRatings = this.selectedRatings.filter(function(
        previousRating
      ) {
        return previousRating.comp_id !== rating.comp_id;
      });
      // Map -1 value ("None" selection) to null.
      if (parseInt(rating.scale_value_id) === -1) {
        rating.scale_value_id = null;
      }
      // Don't add rating when empty option was selected (-2 value).
      if (parseInt(rating.scale_value_id) !== -2) {
        this.selectedRatings.push(rating);
      }

      // Warn user about leaving the page when having unsaved selections.
      if (this.hasSelectedRatings) {
        window.addEventListener('beforeunload', this.unloadHandler);
      } else {
        window.removeEventListener('beforeunload', this.unloadHandler);
      }
    },

    unloadHandler(e) {
      // For older browsers that still show custom message.
      let discardUnsavedChanges = this.$str(
        'unsaved_ratings_warning',
        'pathway_manual'
      );
      e.preventDefault();
      e.returnValue = discardUnsavedChanges;
      return discardUnsavedChanges;
    },

    submitRatings() {
      window.removeEventListener('beforeunload', this.unloadHandler);
      this.showSubmitRatingsModal = false;
      this.$apollo
        .mutate({
          // Query
          mutation: CreateManualRatingsMutation,
          // Parameters
          variables: {
            user_id: this.userId,
            role: this.role,
            ratings: this.selectedRatings,
          },
          refetchAll: false,
        })
        .then(data => {
          if (data.data && data.data.pathway_manual_create_manual_ratings) {
            window.location.href = this.goBackLink;
          } else {
            // TODO Handle this case.
            alert('Something went wrong. Saving failed.');
          }
        })
        .catch(error => {
          // TODO Handle error case
          console.log('error');
          console.error(error);
        });
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
      update({ pathway_manual_user_rateable_competencies: data }) {
        if (data.filters != null) {
          this.filterOptions = data.filters;
        }
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
    margin-bottom: var(--tui-gap-5);
  }

  &__submitButtons {
    float: right;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "filter:no_competencies",
      "number_of_competencies",
      "rate_competencies",
      "rate_user",
      "rate_competencies",
      "rating_as_appraiser",
      "rating_as_manager",
      "modal:submit_ratings_confirmation_title",
      "modal:submit_ratings_confirmation_question",
      "modal:submit_ratings_summary_singular_other",
      "modal:submit_ratings_summary_singular_self",
      "modal:submit_ratings_summary_plural_other",
      "modal:submit_ratings_summary_plural_self",
      "unsaved_ratings_warning"
    ],
    "moodle": [
      "cancel",
      "submit"
    ]
  }
</lang-strings>
