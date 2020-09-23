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
<template>
  <div class="tui-workspaceDiscussionFilter">
    <div class="tui-workspaceDiscussionFilter__search">
      <SearchBox
        v-model="innerSearchTerm"
        :aria-label="$str('search_discussions', 'container_workspace')"
        :label-visible="false"
        :placeholder="$str('search_discussions', 'container_workspace')"
        class="tui-workspaceDiscussionFilter__search-box"
        @submit="$emit('update-search-term', innerSearchTerm)"
      />

      <a
        :href="
          $url('/container/type/workspace/workspace_files.php', {
            id: workspaceId,
          })
        "
        class="tui-workspaceDiscussionFilter__search-filesLink"
      >
        {{ $str('browse_files', 'container_workspace') }}
      </a>
    </div>

    <slot name="pinned-discussions" />

    <div class="tui-workspaceDiscussionFilter__sortBox">
      <SelectFilter
        v-model="innerSort"
        :label="$str('sortby', 'moodle')"
        :show-label="true"
        :options="sortOptions"
      />
    </div>
  </div>
</template>

<script>
import SearchBox from 'tui/components/form/SearchBox';
import SelectFilter from 'tui/components/filters/SelectFilter';

// GraphQL queries
import getDiscussionOptions from 'container_workspace/graphql/discussion_filter_options';

export default {
  components: {
    SearchBox,
    SelectFilter,
  },

  props: {
    searchTerm: String,
    sort: {
      type: String,
      required: true,
    },

    workspaceId: {
      type: [Number, String],
      required: true,
    },
  },

  apollo: {
    sortOptions: {
      query: getDiscussionOptions,
      update({ sorts }) {
        return Array.prototype.map.call(sorts, ({ value, label }) => {
          return {
            label: label,
            id: value,
          };
        });
      },
    },
  },

  data() {
    return {
      innerSearchTerm: this.searchTerm,
      innerSort: this.sort,
      sortOptions: [],
    };
  },

  watch: {
    /**
     *
     * @param {String} value
     */
    sort(value) {
      if (value !== this.innerSort) {
        this.innerSort = value;
      }
    },

    /**
     *
     * @param {String} value
     */
    searchTerm(value) {
      this.innerSearchTerm = value;
    },

    /**
     *
     * @param {String} value
     */
    innerSort(value) {
      if (value !== this.sort) {
        this.$emit('update-sort', value);
      }
    },
  },
};
</script>
<lang-strings>
  {
    "container_workspace": [
      "search_discussions",
      "browse_files"
    ],

    "moodle": [
      "sortby"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-workspaceDiscussionFilter {
  &__search {
    display: flex;
    align-items: center;
    margin-bottom: var(--gap-8);

    &-box {
      flex-basis: 40%;
    }

    &-filesLink {
      margin-left: var(--gap-8);
    }
  }

  &__sortBox {
    display: flex;
    justify-content: flex-end;
  }
}
</style>
