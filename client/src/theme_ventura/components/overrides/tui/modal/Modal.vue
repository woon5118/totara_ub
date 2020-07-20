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
  @module theme_ventura
-->

<style lang="scss">
$tui-modal-smallSize: 400px !default;
$tui-modal-normalSize: 560px !default;
$tui-modal-largeSize: 800px !default;
$tui-modal-sheetBreakpoint: 768px !default;

:root {
  --tui-modal-container-padding: var(--tui-gap-12);
  --tui-modal-sheet-padding: var(--tui-gap-12);
  --tui-modal-border-radius: 0;
}

.tui-modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: var(--tui-zindex-modal);
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  overflow: hidden;
  outline: none;

  &--animated {
    .tui-modal__inner {
      transform: translateY(100vh);
      transition: transform var(--tui-transition-modal-function)
          var(--tui-transition-modal-duration),
        opacity var(--tui-transition-modal-function)
          var(--tui-transition-modal-duration);
    }

    &.tui-modal--in .tui-modal__inner {
      transform: translateY(0);
    }
  }

  &.tui-modal--size-sheet {
    &.tui-modal--animated {
      &.tui-modal--in .tui-modal__inner {
        overflow: scroll;
      }
    }
  }

  &__pad {
    width: 100%;
    height: 100%;
    padding: 0;
  }

  &__inner {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    margin: auto;
    color: var(--tui-color-text);
    background-color: var(--tui-color-background);
    box-shadow: var(--tui-shadow-4);
  }

  &__header {
    display: flex;
    flex-shrink: 0;
  }

  &__close,
  &__outsideClose {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    padding: var(--tui-gap-4);
    font-size: var(--tui-font-size-18);
  }

  &__outsideClose {
    display: none;
    color: var(--tui-color-backdrop-contrast);
  }

  &__outsideClose:hover,
  &__outsideClose:focus {
    color: var(--tui-color-backdrop-contrast);
    opacity: 0.8;
  }
}

.has-tui-modal {
  overflow: hidden;
}

.tui-modalBackdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: var(--tui-zindex-modal-backdrop);
  width: 100%;
  height: 100%;

  &--shade {
    background-color: var(--tui-color-backdrop-standard);
    &.tui-modalBackdrop--size-sheet {
      background-color: var(--tui-color-backdrop-heavy);
    }
  }

  &--animated {
    opacity: 0;
    transition: opacity var(--tui-transition-modal-function)
      var(--tui-transition-modal-duration);

    &.tui-modalBackdrop--in {
      opacity: 1;
    }
  }
}

@media (min-width: $tui-modal-sheetBreakpoint) {
  .tui-modal.tui-modal--size-sheet {
    &.tui-modal--animated {
      .tui-modal__inner {
        transform: scale(0.9);
        opacity: 0;
        transition: transform var(--tui-transition-modal-function)
            var(--tui-transition-modal-duration),
          opacity var(--tui-transition-modal-function)
            var(--tui-transition-modal-duration);
      }

      &.tui-modal--in .tui-modal__inner {
        transform: none;
        opacity: 1;
      }

      .tui-modal__outsideClose {
        opacity: 0;
        transition: opacity var(--tui-transition-modal-function)
          var(--tui-transition-modal-duration);
      }

      &.tui-modal--in .tui-modal__outsideClose {
        opacity: 1;
      }

      &.tui-modal--in .tui-modal__outsideClose:hover,
      &.tui-modal--in .tui-modal__outsideClose:focus {
        opacity: 0.8;
      }
    }

    .tui-modal {
      &__pad {
        padding: var(--tui-modal-sheet-padding);
      }

      &__inner {
        border-radius: var(--tui-modal-border-radius);
      }

      &__close {
        display: none;
      }

      &__outsideClose {
        display: flex;
      }
    }
  }
}

@mixin tui-modal-size($name, $width) {
  @media (min-width: ($width + 75px)) {
    .tui-modal.tui-modal--size-#{$name} {
      overflow-y: auto;

      &.tui-modal--always-scroll {
        overflow-y: scroll;
      }

      &.tui-modal--animated {
        .tui-modal__inner {
          transform: scale(0.9);
          opacity: 0;
          transition: transform var(--tui-transition-modal-function)
              var(--tui-transition-modal-duration),
            opacity var(--tui-transition-modal-function)
              var(--tui-transition-modal-duration);
        }

        &.tui-modal--in .tui-modal__inner {
          transform: none;
          opacity: 1;
        }
      }

      // a separate __pad element is required as flexbox centering with
      // `margin-top/bottom: auto;` and padding on the parent are not compatible
      .tui-modal {
        &__pad {
          height: auto;
          margin: auto;
          padding: var(--tui-modal-container-padding) 0;
        }

        &__inner {
          width: $width;
          height: auto;
          border-radius: var(--tui-modal-border-radius);
        }

        &__close {
          display: none;
        }

        &__outsideClose {
          display: flex;
        }
      }
    }
  }
}

@include tui-modal-size('small', $tui-modal-smallSize);
@include tui-modal-size('normal', $tui-modal-normalSize);
@include tui-modal-size('large', $tui-modal-largeSize);
</style>
