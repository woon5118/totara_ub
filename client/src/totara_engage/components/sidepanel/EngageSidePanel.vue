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
  @module totara_engage
-->
<template>
  <div class="tui-engageSidePanel">
    <slot name="modal">
      <!-- This is where all the modal should be. -->
    </slot>
    <slot name="author-profile" />

    <Tabs
      v-model="selectedTab"
      :small-tabs="true"
      class="tui-engageSidePanel__tabs"
    >
      <Tab
        id="overview"
        class="tui-engageSidePanel__overviewBox"
        :name="$str('overview', 'totara_engage')"
      >
        <slot name="overview" />
      </Tab>

      <Tab
        id="comments"
        class="tui-engageSidePanel__commentBox"
        :name="$str('comments', 'totara_engage')"
      >
        <slot name="comments" />
      </Tab>

      <Tab
        v-if="$slots.related"
        id="related"
        class="tui-engageSidePanel__related"
        :name="$str('related', 'totara_engage')"
      >
        <slot name="related" />
      </Tab>
    </Tabs>
  </div>
</template>

<script>
import Tabs from 'tui/components/tabs/Tabs';
import Tab from 'tui/components/tabs/Tab';

export default {
  components: {
    Tabs,
    Tab,
  },

  data() {
    return {
      selectedTab: 'overview',
    };
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "comments",
      "overview",
      "related"
    ]
  }
</lang-strings>
