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
  <div class="tui-totaraEngage-statIcon">
    <span
      class="tui-totaraEngage-statIcon__iconContainer"
      role="img"
      :aria-label="title"
    >
      <Icon
        class="tui-totaraEngage-statIcon__icon"
        :icon="icon"
        :title="title"
        :icon-size="iconSize"
      />
    </span>

    <span class="tui-totaraEngage-statIcon__statNumber" aria-hidden="true">
      {{ statNumber }}
    </span>
  </div>
</template>

<script>
import Icon from 'totara_engage/components/icons/Icon';

export default {
  components: {
    Icon: Icon,
  },

  props: {
    icon: {
      required: true,
      type: String,
    },

    title: {
      required: true,
      type: String,
    },

    statNumber: {
      required: true,
      type: [String, Number],
    },

    iconSize: {
      type: [String, Number],
    },
  },
};
</script>

<style lang="scss">
.tui-totaraEngage-statIcon {
  display: flex;
  flex-direction: column;
  align-items: center;

  &__statNumber {
    font-size: var(--font-size-12);
  }
}
</style>
