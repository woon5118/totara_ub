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
    <template v-if="hasAvatar">
      <a
        v-if="!readOnly && profileUrl"
        :href="profileUrl"
        class="tui-miniProfileCard__avatar"
      >
        <Avatar :src="avatarSrc" :alt="avatarAlt" size="xsmall" />
      </a>

      <Avatar
        v-else
        :src="avatarSrc"
        :alt="avatarAlt"
        size="xsmall"
        class="tui-miniProfileCard__avatar"
      />
    </template>

    <div class="tui-miniProfileCard__description">
      <template v-for="({ value, url }, index) in displayFields">
        <template v-if="!!value">
          <div
            :key="index"
            class="tui-miniProfileCard__description__row"
            :class="{
              'tui-miniProfileCard__description__row--withGap': index === 1,
            }"
          >
            <p
              v-if="!url || readOnly"
              class="tui-miniProfileCard__description__row__text"
              :class="{
                'tui-miniProfileCard__description__row__text--bold':
                  index === 0,
              }"
            >
              {{ value }}
            </p>

            <a
              v-else
              :href="url"
              class="tui-miniProfileCard__description__row__link"
              :class="{
                'tui-miniProfileCard__description__row__link--bold':
                  index === 0,
              }"
            >
              {{ value }}
            </a>

            <template v-if="index == 0">
              <!-- Add support for tag on the first section. -->
              <slot name="tag" />
            </template>
          </div>
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
import More from 'tui/components/icons/More';

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
    readOnly: Boolean,
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

<style lang="scss">
.tui-miniProfileCard {
  // The parent who uses this card decides the width/height.
  display: flex;
  align-items: flex-start;
  padding: var(--gap-2);
  outline: none;

  &--no-avatar {
    padding-left: var(--gap-4);
  }

  &--no-dropDown {
    padding-right: var(--gap-4);
  }

  &--no-padding {
    // Reset padding to zero.
    padding: 0;
  }

  &--border {
    border: var(--border-width-thin) solid var(--color-neutral-5);
    border-radius: var(--border-radius-normal);
  }

  &--hasShadow {
    box-shadow: var(--shadow-2);
  }

  &__avatar {
    margin-right: var(--gap-2);
  }

  &__description {
    display: flex;
    flex: 1;
    flex-direction: column;
    overflow: hidden;

    &__row {
      display: flex;
      align-items: center;

      &__text {
        @include tui-font-body-small();
        margin: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;

        &--bold {
          @include tui-font-heavy();
        }
      }

      &__link {
        @include tui-font-link-small();
        margin: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;

        &--bold {
          @include tui-font-heavy();
        }
      }

      &--withGap {
        margin-bottom: var(--gap-1);
      }
    }
  }

  &__dropDown {
    margin-left: var(--gap-4);
  }
}
</style>
