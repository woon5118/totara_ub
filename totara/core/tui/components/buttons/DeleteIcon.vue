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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @module totara_core
-->

<template>
  <ButtonIcon
    :aria-label="ariaLabel || $str('delete', 'moodle')"
    :styleclass="{ transparent: true }"
    @click.prevent="$emit('click', $event)"
  >
    <DeleteIcon :size="size" />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/icons/common/Delete';

export default {
  components: {
    ButtonIcon,
    DeleteIcon,
  },

  props: {
    ariaLabel: String,
    size: {
      type: Number,
      default: 200,
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "delete"
  ]
}
</lang-strings>
