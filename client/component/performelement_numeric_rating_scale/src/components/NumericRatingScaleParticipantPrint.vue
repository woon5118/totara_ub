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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package performelement_numeric_rating_scale
-->
<template>
  <div class="tui-numericRatingScalePrint">
    <Range
      name="response"
      :value="activeRange"
      :default-value="element.data.defaultValue"
      :show-labels="false"
      :min="min"
      :max="max"
    />
    <div class="tui-numericRatingScalePrint__value">
      <NotepadLines :char-length="10"  />
    </div>
  </div>
</template>

<script>
import Range from 'tui/components/form/Range';
import NotepadLines from 'tui/components/form/NotepadLines';

export default {
  components: {
    Range,
    NotepadLines,
  },
  props: {
    responseLines: {
      type: Array,
      required: true,
    },
    element: {
      type: Object,
      required: true,
    },
  },
  computed: {
    /**
     * The minimum value that can be selected.
     *
     * @return {number}
     */
    min() {
      return parseInt(this.element.data.lowValue, 10);
    },
    /**
     * The maximum value that can be selected.
     *
     * @return {number}
     */
    max() {
      return parseInt(this.element.data.highValue, 10);
    },

    /**
     * Has this question been answered.
     *
     * @return {boolean}
     */
    activeRange() {
      if (this.responseLines.length > 0) {
        return this.responseLines[0];
      }
      return null;
    },
  },
};
</script>
