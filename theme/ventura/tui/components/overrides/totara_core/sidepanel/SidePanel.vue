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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-sidePanel {
  position: relative;
  display: flex;
  align-items: center;
  height: 100%;
  transition: max-height var(--tui-transition-sidepanel-scrollsnap-duration)
    var(--tui-transition-sidepanel-scrollsnap-function);

  &--sticky {
    position: sticky;
    top: 0;

    .ie & {
      position: relative;
      top: auto;
    }
  }

  /**
   * Close button, somewhat complicated by the SidePanel being configurably
   * bi-directional and both of those directions also requiring RTL support
   **/
  @mixin attrs-from-right() {
    margin-right: -1px;
    border-right-width: 0;
    border-left-width: 1px;
    border-radius: var(--tui-btn-square-radius) 0 0 var(--tui-btn-square-radius);

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 0;
      border-left-width: 1px;
      box-shadow: -4px 3px 6px 0 rgba(0, 0, 0, 0.2);
    }

    // FlexIcon
    .tui-iconBtn__icon {
      transform: rotate(90deg);
    }
  }
  @mixin attrs-from-left() {
    margin-left: -1px;
    border-right-width: 1px;
    border-left-width: 0;
    border-radius: 0 var(--tui-btn-square-radius) var(--tui-btn-square-radius) 0;

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 1px;
      border-left-width: 0;
      box-shadow: 4px 3px 6px 0 rgba(0, 0, 0, 0.2);
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
    }

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
   *
   **/
  &__inner {
    display: flex;
    height: 100%;
    max-height: 100%;
    padding: var(--tui-gap-8);
    overflow: hidden;
    background-color: var(--tui-color-neutral-3);
    border: 1px solid var(--tui-color-neutral-5);

    .tui-sidePanel--open.tui-sidePanel--overflows & {
      overflow-y: auto;
    }
  }

  /**
   *
   **/

  &__content {
    width: 100%;

    .tui-sidePanel--closed &,
    .tui-sidePanel--closing & {
      opacity: 0;
    }

    .tui-sidePanel--open &,
    .tui-sidePanel--opening & {
      opacity: 1;
    }

    .tui-sidePanel--animated & {
      transition: opacity var(--tui-transition-sidepanel-content-duration)
        var(--tui-transition-sidepanel-content-function);
    }
  }
}
</style>
