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
  <div class="tui-bulkManualRatingRateUserCompetencies">
    <div class="tui-bulkManualRatingRateUserCompetencies__filters">
      <UserCompetenciesFilters
        :filter-options="filterOptions"
        :has-ratings="hasSelectedRatings"
        :is-rating-single-competency="isRatingSingleCompetency"
        @update-filters="applyFilters"
        @go-back="$emit('go-back')"
      />
    </div>
    <Loader :loading="$apollo.loading" />
    <div v-if="!$apollo.loading && competencies.framework_groups.length > 0">
      <FrameworkGroup
        v-for="group in competencies.framework_groups"
        :key="group.framework.id"
        class="tui-bulkManualRatingRateUserCompetencies__frameworkGroup"
        :group="group"
        :role="role"
        :current-user-id="currentUserId"
        :selected-ratings="selectedRatings"
        :is-expanded="expandFrameworkGroups"
        @update-rating="updateRating"
        @delete-rating="deleteRating"
      />
      <div class="tui-bulkManualRatingRateUserCompetencies__submitButtons">
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :text="$str('submit')"
            :disabled="!hasSelectedRatings"
            type="submit"
            @click="showSubmitRatingsModal = true"
          />
          <Button :text="$str('cancel')" @click="$emit('go-back')" />
        </ButtonGroup>
        <ConfirmationModal
          :open="showSubmitRatingsModal"
          :title="
            $str('modal:submit_ratings_confirmation_title', 'pathway_manual')
          "
          @confirm="submitRatings"
          @cancel="showSubmitRatingsModal = false"
        >
          <p>{{ submitRatingsModalMessage }}</p>
          <p>
            {{
              $str(
                'modal:submit_ratings_confirmation_question',
                'pathway_manual'
              )
            }}
          </p>
        </ConfirmationModal>
      </div>
    </div>
    <div
      v-else-if="!$apollo.loading"
      class="tui-bulkManualRatingRateUserCompetencies__noCompetencies"
    >
      {{
        hasSelectedFilters
          ? $str('filter:no_competencies', 'pathway_manual')
          : $str('no_rateable_competencies', 'pathway_manual')
      }}
    </div>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import CreateManualRatingsMutation from 'pathway_manual/graphql/create_manual_ratings.graphql';
import FrameworkGroup from 'pathway_manual/components/FrameworkGroup';
import Loader from 'totara_core/components/loader/Loader';
import RateableCompetenciesQuery from 'pathway_manual/graphql/user_rateable_competencies.graphql';
import UserCompetenciesFilters from 'pathway_manual/components/UserCompetenciesFilters';
import { NONE_OPTION_VALUE, ROLE_SELF } from 'pathway_manual/constants';

/**
 * If there are more than this amount of competencies available to rate,
 * then the framework groups should be collapsed by default for performance reasons.
 *
 * @type {number}
 */
const MAX_COMPETENCIES_TO_DISPLAY = 200;

export default {
  components: {
    Button,
    ButtonGroup,
    ConfirmationModal,
    FrameworkGroup,
    Loader,
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
    assignmentId: {
      type: Number,
    },
  },

  data() {
    let data = {
      competencies: {},
      selectedFilters: {},
      filterOptions: {},
      showSubmitRatingsModal: false,
      selectedRatings: [],
      isRatingSingleCompetency: false,
    };

    if (this.assignmentId != null) {
      data.selectedFilters = {
        assignment_reason: [this.assignmentId],
      };
      data.isRatingSingleCompetency = true;
    }

    return data;
  },

  computed: {
    /**
     * Should the framework groups of competencies be expanded?
     * We don't expand them if there are too many competencies.
     * @returns {boolean}
     */
    expandFrameworkGroups() {
      return this.competencies.count < MAX_COMPETENCIES_TO_DISPLAY;
    },

    /**
     * Are there any draft ratings made that haven't been saved?
     * @returns {boolean}
     */
    hasSelectedRatings() {
      return this.selectedRatings.length > 0;
    },

    /**
     * Are there any filters that have been selected but not applied yet?
     * @returns {boolean}
     */
    hasSelectedFilters() {
      return Object.keys(this.selectedFilters).length > 0;
    },

    /**
     * Message to show to confirm saving ratings.
     * @returns {String}
     */
    submitRatingsModalMessage() {
      let ratingSummary = 'modal:submit_ratings_summary';
      ratingSummary +=
        this.selectedRatings.length === 1 ? '_singular' : '_plural';
      ratingSummary += this.role === ROLE_SELF ? '_self' : '_other';

      let confirmMsgParams = {
        amount: this.selectedRatings.length,
        subject_user: this.user.fullname,
      };

      return this.$str(ratingSummary, 'pathway_manual', confirmMsgParams);
    },
  },

  methods: {
    /**
     * Apply the filters that have been selected.
     * This will reload the competencies listed.
     * @param filters
     */
    applyFilters(filters) {
      this.selectedRatings = [];
      this.selectedFilters = filters;
      this.isRatingSingleCompetency = false;
    },

    /**
     * Add/modify an unsaved draft rating.
     * @param ratingData
     */
    updateRating(ratingData) {
      let previousRating = this.selectedRatings.find(
        ratingObj => ratingObj.comp_id === ratingData.comp_id
      );

      if (previousRating) {
        // Was already rated: update value.
        previousRating.scale_value_id = ratingData.scale_value_id;
        previousRating.comment = ratingData.comment;
      } else {
        // Wasn't in list yet: add to array.
        this.selectedRatings.push(ratingData);
      }

      this.updateUnloadHandler();
    },

    /**
     * Remove an unsaved draft rating.
     * @param compId
     */
    deleteRating(compId) {
      this.selectedRatings = this.selectedRatings.filter(
        previousRating => previousRating.comp_id !== compId
      );

      this.updateUnloadHandler();
    },

    /**
     * Notify the browser and parent component that a rating has been changed.
     */
    updateUnloadHandler() {
      // Warn user about leaving the page when having unsaved selections.
      if (this.hasSelectedRatings) {
        window.addEventListener('beforeunload', this.unloadHandler);
      } else {
        window.removeEventListener('beforeunload', this.unloadHandler);
      }

      this.$emit('has-unsaved-ratings', this.hasSelectedRatings);
    },

    /**
     * Update a comment
     * @param newComment
     */
    updateComment(newComment) {
      let rating = this.selectedRatings.find(
        ratingData => ratingData.comp_id === newComment.comp_id
      );
      if (rating) {
        rating.comment = newComment.comment;
      }
    },

    /**
     * Displays a warning message if the user tries to navigate away without saving.
     * @param {Event} e
     * @returns {string}
     */
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

    /**
     * Get rating data for sending to GQL mutation.
     * @returns {{scale_value_id: (null|*), comment: (string|*), comp_id: *}[]}
     */
    getRatingsForSaving() {
      return this.selectedRatings.map(rating => ({
        comp_id: rating.comp_id,
        // Map "None" to null.
        scale_value_id:
          parseInt(rating.scale_value_id) === NONE_OPTION_VALUE
            ? null
            : rating.scale_value_id,
        comment: typeof rating.comment === 'undefined' ? '' : rating.comment,
      }));
    },

    /**
     * Save the draft ratings the user has made.
     */
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
            this.$emit('go-back');
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
    competencies: {
      query: RateableCompetenciesQuery,
      variables() {
        return {
          user_id: this.user.id,
          role: this.role,
          filters: this.selectedFilters,
        };
      },
      update({ pathway_manual_user_rateable_competencies: competencies }) {
        if (competencies.filters != null) {
          this.filterOptions = competencies.filters;
        }
        return competencies;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "pathway_manual": [
      "filter:no_competencies",
      "modal:submit_ratings_confirmation_title",
      "modal:submit_ratings_confirmation_question",
      "modal:submit_ratings_summary_singular_other",
      "modal:submit_ratings_summary_singular_self",
      "modal:submit_ratings_summary_plural_other",
      "modal:submit_ratings_summary_plural_self",
      "no_rateable_competencies",
      "unsaved_ratings_warning"
    ],
    "moodle": [
      "cancel",
      "submit"
    ]
  }
</lang-strings>
