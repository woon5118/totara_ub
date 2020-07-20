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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-sidePanel {
  position: relative;
  display: flex;
  align-items: center;
  height: 100%;
  overflow: hidden;

  &--animated {
    transition: max-height var(--tui-transition-sidepanel-scrollsnap-duration)
      var(--tui-transition-sidepanel-scrollsnap-function);
  }

  &--sticky {
    position: sticky;
    top: 0;

    .ie & {
      position: relative;
      top: auto;
    }
  }

  // inner content alignment
  &--rtl,
  .dir-rtl .tui-sidePanel--ltr & {
    justify-content: flex-end;
  }
  &--ltr,
  .dir-rtl .tui-sidePanel--rtl & {
    justify-content: flex-start;
  }

  /**
   * Close button, somewhat complicated by the SidePanel being configurably
   * bi-directional and both of those directions also requiring RTL support
   **/
  @mixin attrs-from-right() {
    margin-right: -1px;
    margin-left: 4px; /* ensure focus shadow is not cut off by container */
    border-right-width: 0;
    border-left-width: 1px;
    border-radius: var(--tui-btn-radius) 0 0 var(--tui-btn-radius);

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 0;
      border-left-width: 1px;
      box-shadow: -2px 1px 4px 0 rgba(0, 0, 0, 0.2);
    }

    // FlexIcon
    .tui-iconBtn__icon {
      transform: rotate(90deg);
    }
  }
  @mixin attrs-from-left() {
    margin-right: 4px; /* ensure focus shadow is not cut off by container */
    margin-left: -1px;
    border-right-width: 1px;
    border-left-width: 0;
    border-radius: 0 var(--tui-btn-radius) var(--tui-btn-radius) 0;

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 1px;
      border-left-width: 0;
      box-shadow: 2px 1px 4px 0 rgba(0, 0, 0, 0.2);
    }
    // FlexIcon
    .tui-iconBtn__icon {
      transform: rotate(-90deg);
    }
  }

  &__outsideClose {
    .ie & {
      // height, position and scrolling will degrade in IE11, so the toggle
      // button needs a more appropriate location than "the middle" of the
      // SidePanel, which could be very tall in IE11
      align-self: flex-start;
      max-width: 30px;
    }

    flex-grow: 0;
    min-width: 30px;
    height: auto;
    padding: var(--tui-gap-6) var(--tui-gap-1);
    background-color: var(--tui-color-neutral-3);
    border-color: var(--tui-color-neutral-5);

    .tui-sidePanel--rtl &,
    .dir-rtl .tui-sidePanel--ltr & {
      @include attrs-from-right();
    }

    .tui-sidePanel--ltr &,
    .dir-rtl .tui-sidePanel--rtl & {
      @include attrs-from-left();
    }
  }

  /**
   * A wrapper for content container, which helps with transitions on width
   * while overflowing content is still visible, and providing whitespace
   * between content and the edges of the SidePanel
   **/
  &__inner {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    flex-shrink: 1;
    width: 100%;
    height: 100%;
    max-height: 100%;
    background-color: var(--tui-color-neutral-3);
    border: 1px solid var(--tui-color-neutral-5);

    .tui-sidePanel--open.tui-sidePanel--overflows & {
      overflow-y: auto;
    }

    .tui-sidePanel--closed & {
      max-width: 1px;
      padding-right: 0;
      padding-left: 0;
    }
  }

  /**
   * Transitioned container for arbitrary SidePanel content
   **/
  &__content {
    max-height: 100%;
    overflow: hidden;

    .ie & {
      height: 100%;
    }

    .tui-sidePanel--closed &,
    .tui-sidePanel--closing & {
      opacity: 0;
    }

    .tui-sidePanel--closed & {
      visibility: hidden;
    }

    .tui-sidePanel--open &,
    .tui-sidePanel--opening & {
      opacity: 1;
    }

    .tui-sidePanel--animated & {
      transition: opacity var(--tui-transition-sidepanel-content-duration)
        var(--tui-transition-sidepanel-content-function);
    }

    .tui-sidePanel--open.tui-sidePanel--overflows & {
      overflow-y: auto;
    }
  }
}
</style>
