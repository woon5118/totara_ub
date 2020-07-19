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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module totara_competency
-->

<template>
  <Popover v-if="scale" :triggers="['click']">
    <template v-slot:trigger>
      <Button :styleclass="{ reveal: true }" :text="value.name" />
    </template>
    <RatingScaleOverview :scale="scale" :reverse-values="true" />
  </Popover>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import Popover from 'tui/components/popover/Popover';
import RatingScaleOverview from 'totara_competency/components/RatingScaleOverview';

export default {
  components: {
    Button,
    Popover,
    RatingScaleOverview,
  },

  props: {
    value: {
      required: true,
      type: Object,
    },
    scales: {
      required: true,
      type: Array,
    },
  },

  computed: {
    scale() {
      return this.scales.find(({ id }) => id === this.value.scale_id);
    },
  },
};
</script>
