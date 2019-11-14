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
        :role="role"
        :current-user-id="currentUserId"
        @input="updateRatings"
      />
      <div class="tui-pathwayManual-rateUserCompetencies__submitButtons">
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :text="$str('submit', 'moodle')"
            :disabled="!hasSelected"
            type="submit"
            @click="showModal"
          />
          <Button
            :text="$str('cancel', 'moodle')"
            :disabled="!hasSelected"
            @click="formCancel"
          />
        </ButtonGroup>
      </div>
      <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
        <ConfirmModal
          :num-selected="numSelected"
          :is-for-self="isForSelf"
          :subject-user-fullname="data.user.fullname"
          @confirm-submit="submitRatings"
        />
      </ModalPresenter>
    </div>
    <div v-else>
      <em>{{ $str('no_rateable_competencies', 'pathway_manual') }}</em>
    </div>
  </div>
</template>

<script>
import Button from 'totara_core/presentation/form/Button';
import ButtonGroup from 'totara_core/presentation/form/ButtonGroup';
import ConfirmModal from 'pathway_manual/presentation/ConfirmModal';
import ModalPresenter from 'totara_core/presentation/modal/ModalPresenter';
import ScaleTable from 'pathway_manual/containers/ScaleTable';
import UserHeaderWithPhoto from 'pathway_manual/presentation/UserHeaderWithPhoto';

import CreateManualRatingsMutation from '../../webapi/ajax/create_manual_ratings.graphql';
import RateableCompetenciesQuery from '../../webapi/ajax/rateable_competencies.graphql';

const ROLE_SELF = 'self';

export default {
  components: {
    Button,
    ButtonGroup,
    ConfirmModal,
    ModalPresenter,
    UserHeaderWithPhoto,
    ScaleTable,
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
      modalOpen: false,
      selectedRatings: [],
    };
  },

  computed: {
    hasRateableCompetencies() {
      return this.data.count > 0;
    },

    isForSelf() {
      return this.role === ROLE_SELF;
    },

    hasSelected() {
      return this.selectedRatings.length > 0;
    },

    numSelected() {
      return this.selectedRatings.length;
    },
  },

  methods: {
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
      if (this.hasSelected) {
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
      this.modalOpen = false;
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

    showModal() {
      this.modalOpen = true;
    },
    modalRequestClose() {
      this.modalOpen = false;
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

  &__submitButtons {
    float: right;
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "no_rateable_competencies",
      "number_of_competencies",
      "rate_competencies",
      "rate_user",
      "rate_competencies",
      "rating_as_appraiser",
      "rating_as_manager",
      "unsaved_ratings_warning"
    ],
    "moodle": [
      "cancel",
      "submit"
    ]
  }
</lang-strings>
