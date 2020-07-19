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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module tui
-->

<template>
  <Card
    class="tui-actionCard"
    :class="[stacked && 'tui-actionCard--stacked']"
    :has-shadow="true"
  >
    <div class="tui-actionCard__body">
      <slot name="card-body" />
    </div>
    <div v-if="this.$slots['card-action']" class="tui-actionCard__action">
      <slot name="card-action" />
    </div>
  </Card>
</template>

<script>
import Card from 'tui/components/card/Card';

export default {
  components: {
    Card,
  },

  props: {
    stacked: Boolean,
  },
};
</script>
