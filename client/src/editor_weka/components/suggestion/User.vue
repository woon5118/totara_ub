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
  @module editor_weka
-->

<template>
  <div class="tui-editorWeka-user" :style="positionStyle">
    <Dropdown :separator="true" :open="true" @dismiss="$emit('dismiss')">
      <span class="sr-only">
        {{ $str('matching_users', 'editor_weka') }}:
      </span>

      <template v-if="!$apollo.loading">
        <DropdownItem
          v-for="(user, index) in users"
          :key="index"
          :paddingless="true"
          @click="pickUser(user)"
        >
          <MiniProfileCard :no-border="true" :display="user.card_display" />
        </DropdownItem>
      </template>

      <template v-else>
        <DropdownItem :disabled="true">
          {{ $str('loadinghelp', 'moodle') }}
        </DropdownItem>
      </template>
    </Dropdown>
  </div>
</template>

<script>
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import findUsers from 'editor_weka/graphql/find_users_by_pattern';
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';

export default {
  components: {
    Dropdown,
    DropdownItem,
    MiniProfileCard,
  },

  props: {
    contextId: {
      type: [Number, String],
    },

    component: {
      type: String,
    },

    area: {
      type: String,
    },

    x: {
      // This is offset from left
      required: true,
      type: [Number, String],
    },

    y: {
      // This is offset from top.
      required: true,
      type: [Number, String],
    },

    pattern: {
      required: true,
      type: String,
    },
  },

  apollo: {
    users: {
      query: findUsers,
      variables() {
        return {
          pattern: this.pattern,
          contextid: this.contextId,
          component: this.component,
          area: this.area,
        };
      },
    },
  },

  data() {
    return {
      users: [],
    };
  },

  computed: {
    positionStyle() {
      return {
        left: `${this.x}px`,
        top: `${this.y}px`,
      };
    },
  },

  methods: {
    /**
     *
     * @param {Array} jobAssignments
     */
    getJobAssignment(jobAssignments) {
      if (jobAssignments && jobAssignments.length > 0) {
        let { fullname } = jobAssignments[0];
        return fullname;
      }

      return '';
    },

    /**
     *
     * @param {Number} id
     * @param {String} fullname
     */
    pickUser({ id, fullname }) {
      this.$emit('item-selected', { id, text: fullname });
    },
  },
};
</script>

<lang-strings>
  {
    "editor_weka": [
      "matching_users"
    ],
    "moodle": [
      "loadinghelp"
    ]
  }
</lang-strings>
