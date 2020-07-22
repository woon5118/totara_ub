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
  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module tui
-->

<template>
  <div
    :no-border="noBorder"
    class="tui-miniProfileCard"
    :class="{
      'tui-miniProfileCard--border': !noBorder,
      'tui-miniProfileCard--hasShadow': hasShadow,
      'tui-miniProfileCard--no-padding': noBorder,
      'tui-miniProfileCard--no-avatar': !noBorder && !hasAvatar,
      'tui-miniProfileCard--no-dropDown': !noBorder && !hasDropDown,
    }"
  >
    <!-- Only displaying avatar picture if there are no urls -->
    <a v-if="hasAvatar" :href="profileUrl" class="tui-miniProfileCard__avatar">
      <Avatar
        v-if="hasAvatar"
        :src="avatarSrc"
        :alt="avatarAlt"
        size="xsmall"
      />
    </a>

    <div class="tui-miniProfileCard__description">
      <template v-for="({ value, url }, index) in displayFields">
        <template v-if="!!value">
          <p
            v-if="!url"
            :key="index"
            class="tui-miniProfileCard__description__text"
            :class="{
              'tui-miniProfileCard__description__text--position-zero':
                0 === index,
              'tui-miniProfileCard__description__text--with-gap': 1 === index,
            }"
          >
            {{ value }}
          </p>

          <a
            v-else
            :key="index"
            :href="url"
            class="tui-miniProfileCard__description__link"
            :class="{
              'tui-miniProfileCard__description__link--position-zero':
                0 === index,
              'tui-miniProfileCard__description__text--with-gap': 1 === index,
            }"
          >
            {{ value }}
          </a>
        </template>
      </template>
    </div>

    <Dropdown
      v-if="hasDropDown"
      :position="dropDownPosition"
      class="tui-miniProfileCard__dropDown"
    >
      <template v-slot:trigger="{ toggle, isOpen }">
        <ButtonIcon
          :aria-expanded="isOpen ? 'true' : 'false'"
          :aria-label="dropDownAriaLabel"
          :styleclass="{ transparentNoPadding: true, small: true }"
          @click="toggle"
        >
          <More :size="buttonIconSize" />
        </ButtonIcon>
      </template>

      <slot name="drop-down-items" />
    </Dropdown>
  </div>
</template>

<script>
import Avatar from 'tui/components/avatar/Avatar';
import Dropdown from 'tui/components/dropdown/Dropdown';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import More from 'tui/components/icons/common/More';

export default {
  components: {
    Avatar,
    Dropdown,
    ButtonIcon,
    More,
  },

  props: {
    dropDownPosition: {
      type: String,
      default: 'bottom-right',
    },
    buttonIconSize: String,
    dropDownButtonAriaLabel: String,
    noBorder: Boolean,
    hasShadow: Boolean,
    display: {
      type: Object,
      required: true,
      validator(display) {
        if (
          !('profile_picture_url' in display) ||
          !('profile_picture_alt' in display) ||
          !('profile_url' in display) ||
          !('display_fields' in display)
        ) {
          return false;
        }

        const { display_fields: fields } = display;
        return Array.prototype.every.call(fields, function(field) {
          // Only looking for 'value' and 'associate_url' for now.
          return 'value' in field && 'associate_url' in field;
        });
      },
    },
  },

  computed: {
    /**
     * Normalise the display fields with just associated url and the value.
     *
     * @return {Array}
     */
    displayFields() {
      const { display_fields: fields } = this.display;
      return fields.map(function({ value, associate_url }) {
        return {
          value: value,
          url: associate_url,
        };
      });
    },

    /**
     * @return {String|null}
     */
    avatarSrc() {
      return this.display.profile_picture_url;
    },

    /**
     *
     * @return {String}
     */
    avatarAlt() {
      if (!this.display.profile_picture_alt) {
        return '';
      }

      return this.display.profile_picture_alt;
    },

    /**
     * @return {Boolean}
     */
    hasDropDown() {
      return !!this.$scopedSlots['drop-down-items'];
    },

    /**
     *
     * @return {Boolean}
     */
    hasAvatar() {
      return !!this.avatarSrc;
    },

    /**
     *
     * @return {String}
     */
    dropDownAriaLabel() {
      if (this.dropDownButtonAriaLabel) {
        return this.dropDownButtonAriaLabel;
      }

      return this.$str('actions', 'moodle');
    },

    profileUrl() {
      if (!this.display.profile_url) {
        return '#';
      }

      return this.display.profile_url;
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "actions"
    ]
  }
</lang-strings>
