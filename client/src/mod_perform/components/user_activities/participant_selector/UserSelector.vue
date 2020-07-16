<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

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
import TagList from 'totara_core/components/tag/TagList';

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
