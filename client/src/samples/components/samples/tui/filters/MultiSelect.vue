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
  @module samples
-->

<template>
  <div>
    <MultiSelect v-model="selection" :options="options" :title="'temp title'" />
    <br />
    {{ selection }}
  </div>
</template>

<script>
import MultiSelect from 'tui/components/filters/MultiSelectFilter';

export default {
  components: {
    MultiSelect,
  },

  data() {
    return {
      options: [
        {
          id: 'course',
          label: 'Include courses',
        },
        {
          id: 'program',
          label: 'Include programs',
        },
        {
          id: 'user',
          label: 'Include users',
        },
      ],
      selection: ['program'],
    };
  },

  methods: {},
};
</script>
