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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  --tui-form-toggle-color: var(--tui-color-neutral-7);
  --tui-form-toggle-dot-size: 1.6rem;
  --tui-form-toggle-container-width: 4rem;
  --tui-form-toggle-container-height: 2rem;
  --tui-form-toggle-container-radius: 1rem;
  --tui-form-toggle-text-offset: 5rem;
  --tui-form-toggle-dot-offset: 0.2rem;
  --tui-form-toggle-bottom: 1.8rem;
}

.tui-toggleBtn {
  @extend .tui-formBtn;
  @extend .tui-formBtn--transparent;

  position: relative;
  padding-left: var(--tui-form-toggle-text-offset);
  color: var(--tui-form-toggle-color);

  &__ui {
    position: absolute;
    bottom: var(--tui-form-toggle-bottom);
    left: 0;

    // the toggle background
    &:before {
      position: absolute;
      top: 0;
      left: 0;
      display: block;
      width: var(--tui-form-toggle-container-width);
      height: var(--tui-form-toggle-container-height);
      background-color: var(--tui-form-toggle-off-bg-color);
      border: var(--tui-form-input-border-size) solid;
      border-color: var(--tui-form-toggle-border-color);
      border-radius: var(--tui-form-toggle-container-radius);
      transition: background-color var(--tui-transition-button-duration)
          var(--tui-transition-button-function),
        border-color var(--tui-transition-button-duration)
          var(--tui-transition-button-function);
      content: '';
    }

    // the toggle dot
    &:after {
      position: absolute;
      top: var(--tui-form-toggle-dot-offset);
      left: var(--tui-form-toggle-dot-offset);
      display: block;
      width: var(--tui-form-toggle-dot-size);
      height: var(--tui-form-toggle-dot-size);
      background-color: var(--tui-form-toggle-dot-color);
      border-radius: 50%;
      box-shadow: var(--tui-shadow-2);
      transition: right var(--tui-transition-toggle-duration)
        var(--tui-transition-toggle-function);
      content: '';
    }
  }

  &:focus,
  &:active:focus {
    color: var(--tui-form-toggle-color);
    outline: none;
  }

  &:hover {
    color: var(--tui-form-toggle-color);
  }

  &:hover,
  &:focus {
    .tui-toggleBtn__ui {
      &:before {
        background-color: var(--tui-form-toggle-off-bg-color-hover-focus);
        transition-duration: var(none--tui-transition-form-duration);
      }
    }
  }

  &[aria-pressed='true'] {
    .tui-toggleBtn__ui {
      &:before {
        background-color: var(--tui-form-toggle-on-bg-color);
        border-color: var(--tui-form-toggle-on-border-color);
      }

      &:after {
        left: calc(
          var(--tui-form-toggle-container-width) -
            var(--tui-form-toggle-dot-offset) - var(--tui-form-toggle-dot-size)
        );
        background-color: var(--tui-form-toggle-dot-color);
      }
    }

    &:hover,
    &:focus {
      .tui-toggleBtn__ui {
        &:before {
          background-color: var(--tui-form-toggle-on-bg-color-hover-focus);
          border-color: var(--tui-form-toggle-on-border-color-hover-focus);
          transition-duration: var(none--tui-transition-form-duration);
        }
      }
    }
  }

  &[disabled] {
    opacity: 0.4;
  }
}

.tui-toggleBtn__rtl {
  padding-right: var(--tui-form-toggle-text-offset);
  padding-left: 0;
  .tui-toggleBtn__ui {
    right: 0;

    &:before {
      right: 0;
      left: auto;
    }

    &:after {
      right: calc(
        var(--tui-form-toggle-container-width) -
          var(--tui-form-toggle-dot-offset) - var(--tui-form-toggle-dot-size)
      );
      left: auto;
    }
  }

  &[aria-pressed='true'] {
    .tui-toggleBtn__ui {
      &:after {
        right: var(--tui-form-toggle-dot-offset);
        left: auto;
      }
    }
  }
}
</style>
