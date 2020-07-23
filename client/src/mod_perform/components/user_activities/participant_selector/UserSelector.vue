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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <TagList
    :tags="value"
    :items="userList"
    @select="select"
    @remove="remove"
    @filter="filter = $event"
  >
    <template v-slot:item="{ item }">{{ item.text }}</template>
  </TagList>
</template>

<script>
import TagList from 'tui/components/tag/TagList';

export default {
  components: {
    TagList,
  },

  props: {
    value: {
      type: Array,
      default() {
        return [];
      },
    },
    users: {
      required: true,
      type: Array,
    },
  },

  data() {
    return {
      filter: '',
    };
  },

  computed: {
    /**
     * List of users to display in the dropdown.
     * This gets the users who haven't been selected and meet the filter criteria.
     *
     * @returns {[Object]}
     */
    userList() {
      let userList = this.users.filter(user => {
        return !this.value.some(selectedUser => selectedUser.id === user.id);
      });

      if (this.filter) {
        userList = userList.filter(user =>
          user.fullname.toLowerCase().includes(this.filter.toLowerCase())
        );
      }

      return userList.map(user => {
        return {
          id: user.id,
          text: user.fullname,
        };
      });
    },
  },

  methods: {
    /**
     * Add user to selected values.
     *
     * @param {Object} selectedUser
     */
    select(selectedUser) {
      this.$emit('input', this.value.concat([selectedUser]));
    },

    /**
     * Remove user from selected values.
     *
     * @param {Object} selectedUser
     */
    remove(selectedUser) {
      this.$emit(
        'input',
        this.value.filter(user => user.id !== selectedUser.id)
      );
    },
  },
};
</script>
