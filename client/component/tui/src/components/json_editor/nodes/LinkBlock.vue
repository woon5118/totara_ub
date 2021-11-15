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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-linkBlock">
    <div v-if="attrs.image" class="tui-linkBlock__image" :style="imageStyle" />
    <div class="tui-linkBlock__summary">
      <template v-if="hasInfo">
        <div class="tui-linkBlock__site">
          {{ domain }}
        </div>
        <div class="tui-linkBlock__title">
          {{ attrs.title }}
        </div>
        <div class="tui-linkBlock__description">
          {{ attrs.description }}
        </div>
      </template>
      <div v-else class="tui-linkBlock__linkOnly">
        {{ attrs.url }}
      </div>
    </div>
    <a class="tui-linkBlock__overlayLink" :href="attrs.url">
      <span class="sr-only">{{ attrs.title || attrs.url }}</span>
    </a>
  </div>
</template>

<script>
export default {
  props: {
    attrs: {
      type: Object,
      required: true,
    },
  },

  computed: {
    hasInfo() {
      return !!this.attrs.title;
    },

    domain() {
      const { url } = this.attrs;
      const match = /^https?:\/\/(?:www.)?([^/]+)/.exec(url);
      return match ? match[1] : null;
    },

    imageStyle() {
      return {
        backgroundImage: 'url("' + encodeURI(this.attrs.image) + '")',
      };
    },
  },
};
</script>

<style lang="scss">
.tui-linkBlock {
  position: relative;
  display: flex;
  max-width: 28.6rem;
  height: calc(7.6rem + 2px);
  font-size: var(--font-size-13);
  line-height: 1.15;
  white-space: normal;
  border: 1px solid var(--card-border-color);
  border-radius: 4px;
  transition: box-shadow var(--transition-form-function)
    var(--transition-form-duration);

  &:hover,
  &:focus {
    box-shadow: var(--shadow-2);
  }

  &__image {
    flex-shrink: 0;
    order: 2;
    width: 6rem;
    height: 6rem;
    margin: var(--gap-2);
    background-position: center;
    background-size: cover;
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;

    > img {
      width: 100%;
    }
  }

  &__summary {
    flex-grow: 1;
    order: 1;
    padding: var(--gap-2);
    overflow: hidden;
  }

  &__site {
    margin-bottom: 0.3rem;
    font-weight: bold;
    font-size: var(--font-size-12);
    line-height: 1.15;
  }

  &__title {
    margin-bottom: 0.3rem;
    color: var(--color-state);
    font-weight: bold;
  }

  &__description {
    // show a max of 2 lines
    height: calc(var(--font-size-13) * 2 * 1.15);
    overflow: hidden;
  }

  &__linkOnly {
    // show a max of 4 lines
    height: calc(var(--font-size-13) * 4 * 1.12);
    margin-top: 0.2rem;
    overflow: hidden;
    color: var(--color-state);
    font-weight: bold;
    font-size: var(--font-size-13);
    line-height: 1.15;
  }

  &__overlayLink {
    position: absolute;
    // -1px to account for border
    top: -1px;
    right: -1px;
    bottom: -1px;
    left: -1px;
  }
}
</style>
