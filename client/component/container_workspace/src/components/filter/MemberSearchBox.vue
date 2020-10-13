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
  @module container_workspace
-->
<!-- This component is in place in order to prevent the reactive search term. -->
<template>
  <SearchBox
    v-model="innerSearchTerm"
    :label-visible="false"
    :aria-label="$str('search_members', 'container_workspace')"
    :placeholder="$str('search_members', 'container_workspace')"
    @submit="$emit('submit', innerSearchTerm)"
  />
</template>

<script>
import SearchBox from 'tui/components/form/SearchBox';

export default {
  components: {
    SearchBox,
  },

  props: {
    searchTerm: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      // Clone the search term so that the component won't modify the props.
      innerSearchTerm: this.searchTerm,
    };
  },

  watch: {
    /**
     *
     * @param {String} value
     */
    searchTerm(value) {
      this.innerSearchTerm = value;
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "search_members"
    ]
  }
</lang-strings>
