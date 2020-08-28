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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_competency
-->

<template>
  <div class="tui-competencyUserHeader">
    <Avatar
      :src="profilePicture"
      size="xsmall"
      :alt="userName"
      :aria-hidden="true"
    />
    <div class="tui-competencyUserHeader__name">
      {{ userName }}
    </div>
  </div>
</template>

<script>
import Avatar from 'tui/components/avatar/Avatar';

export default {
  components: {
    Avatar,
  },

  props: {
    userName: String,
    profilePicture: String,
  },
};
</script>

<style lang="scss">
.tui-competencyUserHeader {
  position: relative;
  display: flex;
  align-items: center;

  &::after {
    position: absolute;
    right: 0;
    bottom: 0;
    left: 19px; // half of xsmall avatar (which is specified in px)
    border-bottom: var(--border-width-normal) solid var(--color-primary);
    content: '';
  }

  &__name {
    @include tui-font-body();
    // offset name by the border width so it doesn't look weird
    margin-bottom: var(--border-width-normal);
    margin-left: var(--gap-2);
    font-weight: bold;
  }
}
</style>
