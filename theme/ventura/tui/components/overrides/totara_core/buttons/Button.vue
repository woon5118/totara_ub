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
  // Btn shadow
  --tui-btn-shadow-focus: var(--tui-shadow-2);
  --tui-btn-shadow-active: var(--tui-shadow-3);
}
// Reset
.tui-formBtn {
  display: inline-block;
  align-items: flex-start;
  box-sizing: border-box;
  height: auto;
  padding: 1px 7px 2px;
  overflow: visible;
  color: buttontext;
  font-weight: normal;
  font-size: 14px;
  line-height: normal;
  letter-spacing: normal;
  white-space: normal;
  text-align: center;
  text-transform: none;
  text-decoration: none;
  text-indent: 0;
  text-shadow: none;
  vertical-align: baseline;
  word-spacing: normal;
  background-color: buttonface;
  border-color: rgb(216, 216, 216) rgb(209, 209, 209) rgb(186, 186, 186);
  border-style: solid;
  border-width: 1px;
  border-radius: 0;
  border-image: initial;
  cursor: pointer;
  touch-action: auto;
  text-rendering: auto;

  &:active {
    border-style: inset;
  }
  &:focus {
    outline-width: 5px;
    outline-style: auto;
  }
}

.tui-formBtn,
%tui-formBtn {
  position: relative;
  display: inline-block;
  flex-shrink: 0;
  min-width: 0;
  max-width: 100%;
  min-height: 3.6rem;
  padding: 0 var(--tui-gap-4);

  padding: calc(
      (3.6rem - var(--tui-form-input-font-size) * 1.2) / 2 -
        var(--tui-form-input-border-size)
    )
    var(--tui-gap-4);

  color: var(--tui-btn-text-color);
  font-size: var(--tui-form-input-font-size);
  line-height: 1.2;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: none;
  background: var(--tui-btn-bg-color);
  border: var(--tui-form-input-border-size) solid;
  border-color: var(--tui-btn-border-color);
  border-radius: var(--tui-btn-radius);
  cursor: pointer;
  transition: tui-transition('button', background-color),
    tui-transition('button', border-color), tui-transition('button', box-shadow);

  > .tui-formBtn__caret {
    margin-left: var(--tui-gap-2);
  }

  &:active,
  &:focus,
  &:active:focus,
  &:active:hover,
  &:hover {
    color: var(--tui-btn-text-color-focus);
    text-decoration: none;
    background: var(--tui-btn-bg-color-focus);
    border-color: var(--tui-btn-border-color-focus);
    outline: 0;
    box-shadow: var(--tui-btn-shadow-focus);
  }

  &:active,
  &:active:focus,
  &:active:hover {
    color: var(--tui-btn-text-color-active);
    background: var(--tui-btn-bg-color-active);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-btn-border-color-active);
    box-shadow: var(--tui-btn-shadow-active);
  }

  &:disabled,
  &[disabled] {
    color: var(--tui-btn-text-color-disabled);
    background-color: var(--tui-btn-bg-color-disabled);
    border-color: var(--tui-btn-border-color-disabled);
    cursor: default;
    opacity: 1;

    &:active,
    &:focus,
    &:active:focus,
    &:active:hover,
    &:hover {
      color: var(--tui-btn-text-color-disabled);
      background-color: var(--tui-btn-bg-color-disabled);
      border-color: var(--tui-btn-border-color-disabled);
      box-shadow: none;
    }
  }

  &--alert {
    color: var(--tui-btn-alert-text-color);
    background: var(--tui-btn-alert-bg-color);
    border-color: var(--tui-btn-alert-border-color);

    &:focus,
    &:hover {
      color: var(--tui-btn-alert-text-color-focus);
      background: var(--tui-btn-alert-bg-color-focus);
      border-color: var(--tui-btn-alert-border-color-focus);
    }

    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-btn-alert-text-color-active);
      background: var(--tui-btn-alert-bg-color-active);
      border-color: var(--tui-btn-alert-border-color-active);
    }

    &:disabled,
    &[disabled] {
      color: var(--tui-btn-alert-text-color-disabled);
      background: var(--tui-btn-alert-bg-color-disabled);
      border-color: var(--tui-btn-alert-border-color-disabled);
      cursor: default;
      opacity: 1;

      &:active,
      &:focus,
      &:active:focus,
      &:active:hover,
      &:hover {
        color: var(--tui-btn-alert-text-color-disabled);
        background: var(--tui-btn-alert-bg-color-disabled);
        border-color: var(--tui-btn-alert-border-color-disabled);
        box-shadow: none;
      }
    }
  }

  &--prim {
    color: var(--tui-btn-prim-text-color);
    font-weight: bold;
    background: var(--tui-btn-prim-bg-color);
    border-color: var(--tui-btn-prim-border-color);

    &:focus,
    &:hover {
      color: var(--tui-btn-prim-text-color-focus);
      background: var(--tui-btn-prim-bg-color-focus);
      border-color: var(--tui-btn-prim-border-color-focus);
    }

    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-btn-prim-text-color-active);
      background: var(--tui-btn-prim-bg-color-active);
      border-color: var(--tui-btn-prim-border-color-active);
    }

    &:disabled,
    &[disabled] {
      color: var(--tui-btn-prim-text-color-disabled);
      background: var(--tui-btn-prim-bg-color-disabled);
      border-color: var(--tui-btn-prim-border-color-disabled);
      cursor: default;
      opacity: 1;

      &:active,
      &:focus,
      &:active:focus,
      &:active:hover,
      &:hover {
        color: var(--tui-btn-prim-text-color-disabled);
        background: var(--tui-btn-prim-bg-color-disabled);
        border-color: var(--tui-btn-prim-border-color-disabled);
        box-shadow: none;
      }
    }
  }

  &--small {
    min-height: var(--tui-form-input-height);
    padding: calc(
        (
            var(--tui-form-input-height) - var(--tui-form-input-font-size-sm) *
              1.2
          ) / 2 - var(--tui-form-input-border-size)
      )
      var(--tui-gap-3);

    font-size: var(--tui-form-input-font-size-sm);
    line-height: 1.2;
  }

  &--transparent,
  &--reveal {
    min-height: auto;
    padding: 0;
    line-height: 1;
    border-radius: 0;
  }

  &--transparent,
  &--reveal,
  &--stealth {
    color: var(--tui-color-state);
    background: transparent;
    border: none;
    cursor: pointer;

    &:focus {
      color: var(--tui-color-state-focus);
      text-decoration: none;
      background: transparent;
      border: none;
      box-shadow: none;
    }

    &:hover {
      color: var(--tui-color-state-hover);
      text-decoration: none;
      background: transparent;
      border: none;
      box-shadow: none;
    }

    &:active,
    &:active:hover,
    &:active:focus {
      color: var(--tui-color-state-active);
      text-decoration: none;
      background: transparent;
      border: none;
      box-shadow: none;
    }

    &:active:focus,
    &:focus {
      @include tui-focus();
    }

    &:disabled {
      color: var(--tui-color-state-disabled);
      background: transparent;
      opacity: 1;

      &:active,
      &:focus,
      &:active:focus,
      &:active:hover,
      &:hover {
        color: var(--tui-color-state-disabled);
        background: transparent;
        box-shadow: none;
      }
    }
  }

  &--reveal {
    color: currentColor;
    border-bottom: 1px dashed var(--tui-color-state);

    &:focus,
    &:hover,
    &:active,
    &:active:hover,
    &:active:focus {
      color: currentColor;
      border-bottom: 1px dashed var(--tui-color-state);
    }

    &:disabled {
      color: currentColor;
      border-bottom: 1px dashed var(--tui-color-state);
      opacity: 0.7;

      &:active,
      &:focus,
      &:active:focus,
      &:active:hover,
      &:hover {
        color: currentColor;
        border-bottom: 1px dashed var(--tui-color-state);
      }
    }
  }

  &--toolbar {
    height: var(--tui-gap-6);
    padding: var(--tui-gap-1);
    color: var(--tui-btn-toolbar-text-color);
    font-size: var(--tui-font-size-14);
    line-height: 1;
    background: var(--tui-btn-toolbar-bg-color);
    border: none;
    border-radius: 0;
    transition: none;

    &:focus {
      color: var(--tui-btn-toolbar-text-color);
      background: var(--tui-btn-toolbar-bg-color-focus);
      border: none;
      box-shadow: none;
    }

    &:hover {
      color: var(--tui-btn-toolbar-text-color);
      background: var(--tui-btn-toolbar-bg-color-focus);
      border: none;
      box-shadow: none;
    }

    &:active,
    &:active:hover,
    &:active:focus {
      color: var(--tui-btn-toolbar-text-color);
      text-decoration: none;
      background: var(--tui-btn-toolbar-bg-color-focus);
      border: none;
      box-shadow: none;
    }

    &:disabled {
      color: var(--tui-color-state-disabled);
      background: transparent;
      opacity: 1;

      &:active,
      &:focus,
      &:active:focus,
      &:active:hover,
      &:hover {
        color: var(--tui-color-state-disabled);
        background: transparent;
        box-shadow: none;
      }
    }
  }

  &--toolbar&--selected {
    color: var(--tui-btn-toolbar-text-color-selected);
    background: var(--tui-btn-toolbar-bg-color-selected);
  }

  &--srOnly {
    @include sr-only();
  }
}
</style>
