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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module samples
-->

<template>
  <div class="tui-samplesCtl">
    <Collapsible v-model="expanded" label="Prop options">
      <div class="tui-samplesCtl__box">
        <Form>
          <slot />
        </Form>
      </div>
    </Collapsible>
  </div>
</template>

<script>
import Collapsible from 'tui/components/collapsible/Collapsible';
import Form from 'tui/components/form/Form';

export default {
  components: {
    Collapsible,
    Form,
  },

  data() {
    return {
      expanded: true,
    };
  },
};
</script>

<style lang="scss">
.tui-samplesCtl {
  &__box {
    margin: var(--tui-gap-4) 0;
    padding: var(--tui-gap-4);
    background-color: var(--tui-color-neutral-3);
    border: 1px solid var(--tui-color-neutral-5);
  }

  &__optional {
    margin: var(--tui-gap-4) 0;
    padding-bottom: var(--tui-gap-1);
    border-bottom: 1px solid var(--tui-color-neutral-5);
  }
}
</style>
