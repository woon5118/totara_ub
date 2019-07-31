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

  @author Qingyang Liu <Qingyang.liu@totaralearning.com>
  @module engage_survey
-->
<template>
  <Layout class="tui-surveyVoteView">
    <template v-if="backButton || navigationButtons" v-slot:header>
      <ResourceNavigationBar
        :back-button="backButton"
        :navigation-buttons="navigationButtons"
      />
    </template>
    <template v-slot:column>
      <Loader :loading="$apollo.loading" :fullpage="true" />
      <div v-if="!$apollo.loading" class="tui-surveyVoteView__layout">
        <div class="tui-surveyVoteView__layout__content">
          <SurveyVoteTitle
            :title="firstQuestion.value"
            :bookmarked="bookmarked"
            :owned="survey.owned"
            class="tui-surveyVoteView__layout__content__title"
            @bookmark="updateBookmark"
          />
          <SurveyVoteContent
            v-if="!survey.voted && !survey.owned"
            :answer-type="firstQuestion.answertype"
            :options="firstQuestion.options"
            :question-id="firstQuestion.id"
            :resource-id="resourceId"
            :label="firstQuestion.value"
          />
          <SurveyResultContent v-else :resource-id="resourceId" />
        </div>
      </div>
    </template>

    <template v-slot:sidepanel>
      <SurveySidePanel :resource-id="resourceId" />
    </template>
  </Layout>
</template>

<script>
import Loader from 'tui/components/loader/Loader';
import Layout from 'tui/components/layouts/LayoutOneColumnContentWithSidePanel';
import SurveySidePanel from 'engage_survey/components/sidepanel/SurveySidePanel';
import ResourceNavigationBar from 'totara_engage/components/header/ResourceNavigationBar';
import SurveyVoteTitle from 'engage_survey/components/content/SurveyVoteTitle';
import SurveyVoteContent from 'engage_survey/components/content/SurveyVoteContent';
import SurveyResultContent from 'engage_survey/components/content/SurveyResultContent';
import { surveyPageMixin } from 'engage_survey/index';

export default {
  components: {
    SurveySidePanel,
    ResourceNavigationBar,
    Loader,
    SurveyVoteTitle,
    SurveyVoteContent,
    SurveyResultContent,
    Layout,
  },
  mixins: [surveyPageMixin],
};
</script>
