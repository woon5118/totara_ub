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
  @module theme_ventura
-->

<style lang="scss">
.tui-totaraPlaylist-playlistCard {
  &__imageHeader {
    position: relative;
    width: 100%;
    height: 100%;
  }

  &__imageContainer {
    display: flex;
    flex-wrap: wrap;
    align-content: space-between;
    justify-content: space-between;
    width: 100%;
    height: 100%;
  }

  &__image {
    width: 100%;
    height: 100%;
    background-repeat: no-repeat;
    background-size: cover;
    border-top-left-radius: calc(var(--tui-card-border-radius) - 1px);
    border-top-right-radius: calc(var(--tui-card-border-radius) - 1px);
  }

  &__numberOfResourcesContainer {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }

  &__numberOfResources {
    width: 60px;
    height: 60px;
    background-color: var(--tui-color-neutral-1);
    border-radius: 50%;

    p {
      margin: 0 auto;
      padding: 0;
      font-weight: 700;
      font-size: var(--tui-font-size-16);
      line-height: 60px;
      text-align: center;
    }
  }

  &__icons {
    display: flex;
    flex-direction: row;
    justify-content: center;

    & > * + * {
      margin-left: var(--tui-gap-4);
    }
  }

  &__bookmark {
    // Negative margin here to neutralise the default redundant edges of icon.
    margin-top: -2px;
    margin-right: calc(var(--tui-gap-3) * -1);
  }

  &__title {
    @include tui-font-heading-x-small();
  }

  &__bookmarkIcon {
    margin: -1px -5px 0 0;
    &--hidden {
      visibility: hidden;
    }
  }

  &__rating {
    align-items: flex-end;
    justify-content: flex-start;
    padding-bottom: 10px;

    .tui-totaraEngage-star {
      width: var(--tui-font-size-14);
      height: var(--tui-font-size-14);

      &__filled {
        stop-color: var(--tui-color-chart-background-2);
      }

      &__unfilled {
        stop-color: var(--tui-color-neutral-1);
      }
    }
  }

  &__footer {
    display: flex;
    align-items: flex-end;

    & > * + * {
      margin-left: var(--tui-gap-1);
    }

    & > :last-child {
      margin: 0 -5px 0 auto;
    }
  }
}
</style>
