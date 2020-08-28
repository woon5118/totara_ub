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
  @module totara_engage
-->
<template>
  <article class="tui-baseCard">
    <CoreCard
      class="tui-baseCard__card"
      :clickable="true"
      @mouseover.native="hover(false)"
      @mouseleave.native="hover(false)"
      @click="handleClick(href)"
    >
      <div class="tui-baseCard__headerImage">
        <!-- This is where the image background going to be -->
        <slot name="header-image" />
      </div>

      <div class="tui-baseCard__description">
        <div class="tui-baseCard__description__header">
          <!-- This is where the title will be -->
          <a class="tui-baseCard__link" :href="href">
            <slot name="header" />
          </a>
        </div>

        <div class="tui-baseCard__description__infoContent">
          <!-- This is where the info content (author and such) of the card will be -->
          <slot name="info-content" />
        </div>

        <div class="tui-baseCard__description__footer">
          <!-- This is where the statistic content of the card will be -->
          <slot name="footer" />
        </div>
      </div>
    </CoreCard>
    <Footnotes v-if="showFootnotes" :footnotes="footnotes" />
  </article>
</template>

<script>
import CoreCard from 'tui/components/card/Card';
import Footnotes from 'totara_engage/components/card/Footnotes';

export default {
  components: {
    CoreCard,
    Footnotes,
  },

  props: {
    href: {
      type: String,
      default: '#',
    },

    showFootnotes: {
      type: Boolean,
      default: false,
    },

    /**
     * Footnotes that needs to display below card.
     * Refer to the Footnotes component for more information.
     */
    footnotes: {
      type: Array,
      required: false,
    },
  },

  methods: {
    /**
     * Emitting event on mouse move over and mouse leave.
     * @param {boolean} mouseover
     */
    hover(mouseover) {
      if (mouseover) {
        this.$emit('mouseover');
      } else {
        this.$emit('mouseleave');
      }
    },

    handleClick(href) {
      window.location.href = href;
    },
  },
};
</script>

<style lang="scss">
.tui-baseCard {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;

  &__link {
    color: var(--color-neutral-7);
    text-decoration: none;

    &:hover,
    &:focus {
      color: var(--color-neutral-7);
      text-decoration: none;
      outline: none;
    }
  }

  &__card {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    width: 100%;
    height: 100%;
    cursor: default;
  }

  &__headerImage {
    overflow: hidden;
  }

  &__description {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 0 var(--gap-4) var(--gap-2) var(--gap-4);

    & > :last-child {
      margin-top: auto;
    }

    &__infoContent {
      margin-top: var(--gap-2);
    }
  }
}
</style>
