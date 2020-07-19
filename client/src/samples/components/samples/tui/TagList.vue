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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module samples
-->

<template>
  <div>
    <TagList
      :filter="searchItem"
      class="tui-tagListWrapper"
      :tags="tags"
      :items="items"
      @select="select"
      @remove="remove"
      @filter="filter"
    >
      <template v-if="withAvatar" v-slot:tag="{ tag }">
        <div class="tui-customTag">
          <Avatar :src="tag.url" alt="" size="xsmall" />
          <span>{{ tag.text }}</span>
        </div>
      </template>
      <template v-slot:item="{ index, item }">
        <div>
          <Avatar :src="item.url" alt="" size="medium" />
          <p>{{ item.name }}</p>
        </div>
      </template>
    </TagList>
    <br />
    <Button text="Change Style" @click="withAvatar = !withAvatar" />
  </div>
</template>

<script>
import Avatar from 'tui/components/avatar/Avatar';
import Button from 'tui/components/buttons/Button';
import TagList from 'tui/components/tag/TagList';

export default {
  components: {
    Avatar,
    Button,
    TagList,
  },
  data() {
    return {
      tags: [],
      fetchData: [],
      searchItem: '',
      withAvatar: false,
    };
  },
  computed: {
    items() {
      const items = this.fetchData.filter(
        item => !this.tags.some(tag => item.id === tag.id)
      );
      if (this.searchItem) {
        return items.filter(item =>
          item.name.toUpperCase().includes(this.searchItem.toUpperCase())
        );
      }
      return items;
    },
  },
  mounted() {
    this.fetchData = [
      {
        id: 1,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Mike',
      },
      {
        id: 2,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'John',
      },
      {
        id: 3,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Eric',
      },
      {
        id: 4,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'George',
      },
      {
        id: 5,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Mike',
      },
      {
        id: 6,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'John',
      },
      {
        id: 7,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Eric',
      },
      {
        id: 8,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'George',
      },
      {
        id: 9,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Mike',
      },
      {
        id: 10,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'John',
      },
      {
        id: 11,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        name: 'Eric',
      },
      {
        id: 12,
        url: 'https://i.imgur.com/14WAio1.jpg',
        name: 'George',
      },
    ];
  },
  methods: {
    select(item) {
      const { name, id, url, alt } = item;
      this.tags.push({ text: name, id, url, alt });
    },
    remove(tag) {
      this.tags = this.tags.filter(t => t !== tag);
    },
    filter(value) {
      this.searchItem = value;
    },
  },
};
</script>

<style lang="scss">
.tui-tagListWrapper {
  width: 700px;
}
.tui-customTag {
  padding: 0.4rem;
  border: 1px solid var(--tui-btn-text-color);
  border-radius: 6px;
}
</style>
