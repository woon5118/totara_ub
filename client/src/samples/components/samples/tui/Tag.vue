<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_samples
-->

<template>
  <div>
    <Tag :text="text1">
      <template v-slot:button>
        <ButtonIcon
          :styleclass="{
            transparent: true,
            primary: true,
            small: true,
          }"
          aria-label="tag-remove"
          @click="$emit('remove')"
        >
          <Close size="100" />
        </ButtonIcon>
      </template>
    </Tag>

    <Tag :text="text2" />
    <Tag :text="text3" href="https://www.google.com" />
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Close from 'totara_core/components/icons/common/Close';
import Tag from 'totara_core/components/tag/Tag';

export default {
  components: {
    ButtonIcon,
    Close,
    Tag,
  },
  data() {
    return {
      text1: 'Wellington',
      text2: 'Auckland',
      text3: 'Google',
    };
  },
};
</script>
