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

  @author Cody Finegan <cody.finegan@totaralearning.com>
  @module totara_engage
-->

<template>
  <div class="tui-resourceNavigationBar">
    <a
      v-if="backButton"
      class="tui-resourceNavigationBar__backLink"
      :href="backButton.url"
      :title="backButton.label"
      @click.prevent.stop="backClick"
    >
      <BackArrow size="300" />
      <span> {{ backButton.label }} </span>
    </a>

    <div
      v-if="navigationButtons"
      class="tui-resourceNavigationBar__nextPrevious"
    >
      <a
        v-if="navigationButtons.previous"
        class="tui-resourceNavigationBar__previousLink"
        :href="navigationButtons.previous"
        :title="$str('resourcenavprevious', 'totara_engage')"
      >
        <BackArrow size="100" />
      </a>
      <span
        v-else
        class="tui-resourceNavigationBar__previousLink tui-resourceNavigationBar--disabled"
      >
        <BackArrow size="100" />
      </span>

      <span>{{ navigationButtons.label }}</span>

      <a
        v-if="navigationButtons.next"
        class="tui-resourceNavigationBar__nextLink"
        :href="navigationButtons.next"
        :title="$str('resourcenavnext', 'totara_engage')"
      >
        <ForwardArrow size="100" />
      </a>
      <span
        v-else
        class="tui-resourceNavigationBar__nextLink tui-resourceNavigationBar--disabled"
      >
        <ForwardArrow size="100" />
      </span>
    </div>
  </div>
</template>

<script>
import ForwardArrow from 'tui/components/icons/common/ForwardArrow';
import BackArrow from 'tui/components/icons/common/BackArrow';

export default {
  components: {
    ForwardArrow,
    BackArrow,
  },
  props: {
    backButton: {
      type: Object,
      required: false,
    },

    navigationButtons: {
      type: Object,
      required: false,
    },
  },
  methods: {
    /**
     * Workaround for the catalog, we must use browser history
     * for the back button instead. This will be addressed later,
     * it's a workaround for now.
     */
    backClick() {
      if (this.backButton.history) {
        window.history.back();
        return;
      }

      window.location.href = this.backButton.url;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "resourcenavnext",
      "resourcenavprevious"
    ]
  }
</lang-strings>
