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
    <div class="tui-pathwayManual-rateCompetencies__filters">
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
        :selected-ratings="selectedRatings"
        :expanded="expandFrameworkGroups"
        @input="updateRating"
        @update-comment="updateComment"
      />
      <div class="tui-pathwayManual-rateCompetencies__submitButtons">
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
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ConfirmModal from 'pathway_manual/components/ConfirmModal';
import FrameworkGroup from 'pathway_manual/components/FrameworkGroup';
import UserCompetenciesFilters from 'pathway_manual/components/UserCompetenciesFilters';

import CreateManualRatingsMutation from '../../webapi/ajax/create_manual_ratings.graphql';
import RateableCompetenciesQuery from '../../webapi/ajax/user_rateable_competencies.graphql';

import {
  NONE_OPTION_VALUE,
  EMPTY_OPTION_VALUE,
} from 'totara_competency/components/ScaleSelect';

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
  },

  props: {
    user: {
      required: true,
      type: Object,
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
    assignmentId: {
      required: false,
      type: Number,
    },
  },

  data() {
    let selectedFilters = {};
    if (this.assignmentId != null) {
      selectedFilters = {
        assignment_reason: [this.assignmentId],
      };
    }

    return {
      data: {},
      selectedFilters: selectedFilters,
      filterOptions: null,
      showSubmitRatingsModal: false,
      selectedRatings: [],
      noneOptionValue: NONE_OPTION_VALUE,
      emptyOptionValue: EMPTY_OPTION_VALUE,
    };
  },

  computed: {
    isLoaded() {
      return this.data.count != null;
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
        subject_user: this.user.fullname,
      };

      return this.$str(ratingSummary, 'pathway_manual', confirmMsgParams);
    },
  },

  methods: {
    applyFilters(filters) {
      this.selectedRatings = [];
      this.selectedFilters = filters;
    },

    formCancel() {
      window.location.href = this.goBackLink;
    },

    updateRating(rating) {
      if (parseInt(rating.scale_value_id) === this.emptyOptionValue) {
        // Empty option: Remove rating from array.
        this.selectedRatings = this.selectedRatings.filter(
          previousRating => previousRating.comp_id !== rating.comp_id
        );
      } else {
        let previousRating = this.selectedRatings.find(
          ratingObj => ratingObj.comp_id === rating.comp_id
        );
        if (previousRating) {
          // Was already rated: update value.
          previousRating.scale_value_id = rating.scale_value_id;
        } else {
          // Wasn't in list yet: add to array.
          rating.comment =
            typeof rating.comment === 'undefined' ? '' : rating.comment;
          this.selectedRatings.push(rating);
        }
      }

      // Warn user about leaving the page when having unsaved selections.
      if (this.hasSelectedRatings) {
        window.addEventListener('beforeunload', this.unloadHandler);
      } else {
        window.removeEventListener('beforeunload', this.unloadHandler);
      }

      this.$emit('has-unsaved-ratings', this.hasSelectedRatings);
    },

    updateComment(newComment) {
      let rating = this.selectedRatings.find(
        ratingData => ratingData.comp_id === newComment.comp_id
      );
      if (rating) {
        rating.comment = newComment.comment;
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

    // Get rating data for sending to GQL mutation.
    getRatingsForSaving() {
      // Throw out invalid elements.
      let ratings = this.selectedRatings.filter(
        rating =>
          typeof rating.comp_id !== 'undefined' &&
          rating.scale_value_id !== this.emptyOptionValue
      );
      return ratings.map(rating => ({
        comp_id: rating.comp_id,
        // Map "None" to null.
        scale_value_id:
          parseInt(rating.scale_value_id) === this.noneOptionValue
            ? null
            : rating.scale_value_id,
        comment: typeof rating.comment === 'undefined' ? '' : rating.comment,
      }));
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
            user_id: this.user.id,
            role: this.role,
            ratings: this.getRatingsForSaving(),
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
          user_id: this.user.id,
          role: this.role,
          filters: this.selectedFilters,
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
.tui-pathwayManual-rateCompetencies {
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
