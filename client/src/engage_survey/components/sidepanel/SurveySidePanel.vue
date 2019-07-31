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
  <div class="tui-surveySidePanel">
    <template v-if="!$apollo.loading">
      <ModalPresenter
        :open="openModalFromAction"
        @request-close="openModalFromAction = false"
      >
        <EngageWarningModal
          :message-content="$str('deletewarningmsg', 'engage_survey')"
          @delete="handleDelete"
        />
      </ModalPresenter>

      <MiniProfileCard
        :no-border="true"
        :display="survey.resource.user.card_display"
        class="tui-surveySidePanel__profile"
      >
        <template v-if="survey.owned" v-slot:drop-down-items>
          <DropdownItem @click="openModalFromAction = true">
            {{ $str('deletesurvey', 'engage_survey') }}
          </DropdownItem>
        </template>
      </MiniProfileCard>

      <Tabs :transparent-tabs="true" class="tui-surveySidePanel__tabs">
        <Tab
          id="overview"
          :name="$str('overview', 'totara_engage')"
          :disabled="true"
          class="tui-surveySidePanel__tabs__overview"
        >
          <p class="tui-surveySidePanel__tabs__overview__timeDescription">
            {{ survey.timedescription }}
          </p>
          <AccessSetting
            v-if="survey.owned"
            :item-id="resourceId"
            component="engage_survey"
            :access-value="survey.resource.access"
            :topics="survey.topics"
            :submitting="false"
            :open-modal="openModalFromButtonLabel"
            :enable-time-view="false"
            @close-modal="openModalFromButtonLabel = false"
            @access-update="updateAccess"
          />
          <AccessDisplay
            v-else
            :access-value="survey.resource.access"
            :topics="survey.topics"
            :show-button="false"
          />
          <MediaSetting
            :owned="survey.owned"
            :access-value="survey.resource.access"
            :instance-id="resourceId"
            :shared-by-count="survey.sharedbycount"
            :like-button-aria-label="likeButtonLabel"
            :liked="survey.reacted"
            component-name="engage_survey"
            @access-update="updateAccess"
            @access-modal="openModalFromButtonLabel = true"
            @update-like-status="updateLikeStatus"
          />
        </Tab>
      </Tabs>
    </template>
  </div>
</template>

<script>
import AccessSetting from 'totara_engage/components/sidepanel/access/AccessSetting';
import AccessDisplay from 'totara_engage/components/sidepanel/access/AccessDisplay';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import EngageWarningModal from 'totara_engage/components/modal/EngageWarningModal';
import MediaSetting from 'totara_engage/components/sidepanel/media/MediaSetting';
import apolloClient from 'tui/apollo_client';
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import Tabs from 'tui/components/tabs/Tabs';
import Tab from 'tui/components/tabs/Tab';
import DropdownItem from 'tui/components/dropdown/DropdownItem';

// GraphQL
import getSurvey from 'engage_survey/graphql/get_survey';
import deleteSurvey from 'engage_survey/graphql/delete_survey';
import updateSurvey from 'engage_survey/graphql/update_survey';

export default {
  components: {
    AccessDisplay,
    ModalPresenter,
    EngageWarningModal,
    AccessSetting,
    MediaSetting,
    MiniProfileCard,
    Tabs,
    Tab,
    DropdownItem,
  },

  props: {
    resourceId: {
      type: [Number, String],
      required: true,
    },
  },

  apollo: {
    survey: {
      query: getSurvey,
      fetchPolicy: 'network-only',
      variables() {
        return {
          resourceid: this.resourceId,
        };
      },
    },
  },

  data() {
    return {
      survey: {},
      submitting: false,
      openModalFromButtonLabel: false,
      openModalFromAction: false,
    };
  },

  computed: {
    userEmail() {
      return this.survey.resource.user.email || '';
    },
    sharedByCount() {
      return this.survey.sharedByCount;
    },

    likeButtonLabel() {
      if (this.survey.reacted) {
        return this.$str(
          'removelikesurvey',
          'engage_survey',
          this.survey.resource.name
        );
      }

      return this.$str(
        'likesurvey',
        'engage_survey',
        this.survey.resource.name
      );
    },
  },

  methods: {
    handleDelete() {
      this.$apollo
        .mutate({
          mutation: deleteSurvey,
          variables: {
            resourceid: this.resourceId,
          },
          refetchAll: false,
        })
        .then(({ data }) => {
          if (data.result) {
            this.openModalFromAction = false;
            window.location.href = this.$url(
              '/totara/engage/your_resources.php'
            );
          }
        });
    },

    /**
     * Updates Access for this survey
     *
     * @param {String} access The access level of the survey
     * @param {Array} topics Topics that this survey should be shared with
     * @param {Array} shares An array of group id's that this survey is shared with
     */
    updateAccess({ access, topics, shares }) {
      this.submitting = true;
      this.$apollo
        .mutate({
          mutation: updateSurvey,
          refetchAll: false,
          variables: {
            resourceid: this.resourceId,
            access: access,
            topics: topics.map(({ id }) => id),
            shares: shares,
          },

          update: (proxy, { data }) => {
            proxy.writeQuery({
              query: getSurvey,
              variables: { resourceid: this.resourceId },
              data,
            });
          },
        })
        .finally(() => {
          this.submitting = false;
        });
    },

    /**
     *
     * @param {Boolean} status
     */
    updateLikeStatus(status) {
      let { survey } = apolloClient.readQuery({
        query: getSurvey,
        variables: {
          resourceid: this.resourceId,
        },
      });

      survey = Object.assign({}, survey);
      survey.reacted = status;

      apolloClient.writeQuery({
        query: getSurvey,
        variables: { resourceid: this.resourceId },
        data: { survey: survey },
      });
    },
  },
};
</script>
<lang-strings>
  {
    "engage_survey": [
      "deletewarningmsg",
      "likesurvey",
      "removelikesurvey",
      "deletesurvey"
    ],
    "totara_engage": [
      "overview"
    ]
  }
</lang-strings>
