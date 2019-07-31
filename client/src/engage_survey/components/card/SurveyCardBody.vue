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
  <div class="tui-surveyCardBody">
    <div :id="labelId" class="tui-surveyCardBody__title">
      {{ name }}
    </div>
    <div class="tui-surveyCardBody__footer">
      <p v-if="showEdit" class="tui-surveyCardBody__text">
        {{ $str('noresult', 'engage_survey') }}
      </p>
      <div class="tui-surveyCardBody__container">
        <ActionLink
          v-if="!voted"
          :href="
            $url(url, {
              page: 'vote',
            })
          "
          :text="$str('votenow', 'engage_survey')"
          :styleclass="{ primary: true }"
        />
        <ActionLink
          v-else-if="showEdit"
          :href="
            $url(url, {
              page: 'edit',
            })
          "
          :styleclass="{ primary: true, small: true }"
          :text="$str('editsurvey', 'engage_survey')"
          :aria-label="$str('editsurveyaccessiblename', 'engage_survey', name)"
        />
        <div class="tui-surveyCardBody__icon">
          <AccessIcon :access="access" size="300" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AccessIcon from 'totara_engage/components/icons/access/computed/AccessIcon';
import ActionLink from 'tui/components/links/ActionLink';

export default {
  components: {
    AccessIcon,
    ActionLink,
  },

  inheritAttrs: false,

  props: {
    resourceId: {
      type: [Number, String],
      required: true,
    },

    name: {
      required: true,
      type: String,
      default: '',
    },

    access: {
      required: true,
      type: String,
    },

    voted: {
      required: true,
      type: Boolean,
    },

    owned: {
      required: true,
      type: Boolean,
    },

    editAble: {
      required: true,
      type: Boolean,
    },

    bookmarked: {
      type: Boolean,
      default: false,
    },

    labelId: {
      type: String,
      default: '',
    },

    url: {
      type: String,
      default: '/totara/engage/resources/survey/index.php',
    },
  },
  computed: {
    showEdit() {
      return this.owned && this.editAble;
    },
  },
};
</script>

<lang-strings>
  {
    "engage_survey": [
      "votenow",
      "editsurvey",
      "editsurveyaccessiblename",
      "noresult"
    ]
  }
</lang-strings>
