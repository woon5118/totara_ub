<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module engage_survey
-->

<template>
  <div class="tui-surveyCard">
    <CoreCard
      :clickable="!editAble && voted"
      class="tui-surveyCard__cardContent"
    >
      <div class="tui-surveyCard__cardContent__inner">
        <div class="tui-surveyCard__cardContent__inner__header">
          <section class="tui-surveyCard__cardContent__inner__header__image">
            <img :alt="name" :src="extraData.image" />
            <h3 class="tui-surveyCard__cardContent__inner__header__title">
              {{ $str('survey', 'engage_survey') }}
            </h3>
          </section>
          <BookmarkButton
            v-show="!owned && !editAble"
            size="300"
            :bookmarked="innerBookMarked"
            :primary="false"
            :circle="false"
            :small="true"
            :transparent="true"
            class="tui-surveyCard__cardContent__inner__header__bookmark"
            @click="updateBookmark"
          />
        </div>
        <template v-if="voted && !editAble">
          <SurveyResultBody
            :name="name"
            :label-id="labelId"
            :questions="questions"
            :access="access"
            :resource-id="instanceId"
            :url="url"
            @open-result="show.result = true"
          />
        </template>
        <template v-else>
          <SurveyCardBody
            :name="name"
            :questions="questions"
            :resource-id="instanceId"
            :bookmarked="innerBookMarked"
            :voted="voted"
            :topics="topics"
            :access="access"
            :owned="owned"
            :edit-able="editAble"
            :label-id="labelId"
            :url="url"
            @voted="handleVoted"
          />
        </template>
      </div>
    </CoreCard>
    <Footnotes v-if="showFootnotes" :footnotes="footnotes" />
  </div>
</template>

<script>
import CoreCard from 'tui/components/card/Card';
import { cardMixin } from 'totara_engage/index';
import SurveyCardBody from 'engage_survey/components/card/SurveyCardBody';
import SurveyResultBody from 'engage_survey/components/card/SurveyResultBody';
import BookmarkButton from 'totara_engage/components/buttons/BookmarkButton';

// GraphQL
import voteResult from 'engage_survey/graphql/vote_result';
import updateBookmark from 'totara_engage/graphql/update_bookmark';
import Footnotes from 'totara_engage/components/card/Footnotes';

export default {
  components: {
    CoreCard,
    SurveyCardBody,
    SurveyResultBody,
    BookmarkButton,
    Footnotes,
  },

  mixins: [cardMixin],

  data() {
    let extraData = {},
      questions = [];

    if (this.extra) {
      extraData = JSON.parse(this.extra);
    }

    if (extraData.questions) {
      questions = Array.prototype.slice.call(extraData.questions);
    }

    return {
      show: {
        result: false,
        editModal: false,
      },

      innerBookMarked: this.bookmarked,
      questions: questions,
      voted: extraData.voted || false,
      extraData: JSON.parse(this.extra),
    };
  },

  computed: {
    editAble() {
      const extra = this.extraData;
      return extra.editable || false;
    },
  },

  methods: {
    /**
     * Updating the questions of this cards.
     */
    handleVoted() {
      this.$apollo
        .query({
          query: voteResult,
          variables: {
            resourceid: this.instanceId,
          },
        })
        .then(({ data: { questions } }) => {
          this.questions = questions;
          this.voted = true;

          // Showing the result afterward.
          this.show.result = true;
        });
    },

    $_hideModals() {
      this.show.editModal = false;
      this.show.result = false;
    },

    deleted() {
      this.$_hideModals();

      // Sent to up-stream to remove this very card from very
      this.emitDeleted();
    },

    updated() {
      this.$_hideModals();

      // Sent to up-stream to update this very card.
      this.emitUpdated();
    },

    updateBookmark() {
      this.innerBookMarked = !this.innerBookMarked;
      this.$apollo.mutate({
        mutation: updateBookmark,
        refetchAll: false,
        refetchQueries: ['totara_engage_contribution_cards'],
        variables: {
          itemid: this.instanceId,
          component: 'engage_survey',
          bookmarked: this.innerBookMarked,
        },
      });
    },
  },
};
</script>
<lang-strings>
  {
    "engage_survey": [
      "survey"
    ]
  }
</lang-strings>
