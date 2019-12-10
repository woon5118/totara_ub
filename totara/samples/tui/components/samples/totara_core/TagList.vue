<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_samples
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
          <Avatar :src="tag.url" :alt="tag.alt" size="xsmall" />
          <span>{{ tag.text }}</span>
        </div>
      </template>
      <template v-slot:item="{ index, item }">
        <div>
          <Avatar :src="item.url" :alt="item.alt" size="medium" />
          <p>{{ item.name }}</p>
        </div>
      </template>
    </TagList>
    <br />
    <Button text="Change Style" @click="withAvatar = !withAvatar" />
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';
import Button from 'totara_core/components/buttons/Button';
import TagList from 'totara_core/components/tag/TagList';

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
        alt: 'itemAlt1',
        name: 'Mike',
      },
      {
        id: 2,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt2',
        name: 'John',
      },
      {
        id: 3,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'itemAlt3',
        name: 'Eric',
      },
      {
        id: 4,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt4',
        name: 'George',
      },
      {
        id: 5,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'itemAlt1',
        name: 'Mike',
      },
      {
        id: 6,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt2',
        name: 'John',
      },
      {
        id: 7,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'itemAlt3',
        name: 'Eric',
      },
      {
        id: 8,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt4',
        name: 'George',
      },
      {
        id: 9,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'itemAlt1',
        name: 'Mike',
      },
      {
        id: 10,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt2',
        name: 'John',
      },
      {
        id: 11,
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'itemAlt3',
        name: 'Eric',
      },
      {
        id: 12,
        url: 'https://i.imgur.com/14WAio1.jpg',
        alt: 'itemAlt4',
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
