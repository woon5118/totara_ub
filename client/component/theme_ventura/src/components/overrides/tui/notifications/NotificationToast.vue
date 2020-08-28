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

  @author Arshad Anwer <arshad.anwer@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
@mixin tui-notification-toast-color($name, $color) {
  .tui-notificationToast {
    &--#{$name} {
      background: $color;
    }

    &--#{$name} &__icon {
      background: $color;
    }
  }
}

.tui-notificationToast {
  @include tui-font-body-small();

  display: flex;
  color: var(--tui-toast-color-text);
  background-color: var(--tui-color-prompt-success);
  border-radius: var(--tui-border-radius-small);
  box-shadow: var(--tui-shadow-3);

  &__icon {
    display: flex;
    padding: var(--tui-gap-4) 0 var(--tui-gap-4) var(--tui-gap-4);
    color: var(--tui-color-neutral-1);
    background: var(--tui-color-prompt-success);
    // -1px to avoid isue with razor thin white line between icon container and notification border
    border-top-left-radius: calc(
      var(--tui-border-radius-small) - var(--tui-border-width-thin) - 1px
    );
    border-bottom-left-radius: calc(
      var(--tui-border-radius-small) - var(--tui-border-width-thin) - 1px
    );
  }

  &__message {
    display: flex;
    flex: 1;
    align-items: center;
    padding: var(--tui-gap-4) var(--tui-gap-2);
  }

  &__dismiss {
    display: flex;

    &_button {
      color: var(--tui-color-neutral-4);

      &:hover {
        color: var(--tui-color-neutral-1);
      }
    }
  }
}

@include tui-notification-toast-color('error', var(--tui-color-prompt-alert));

@media screen and (min-width: $tui-screen-sm) {
  .tui-notificationToast {
    @include tui-font-body();
    color: var(--tui-toast-color-text);

    border-radius: var(--tui-border-radius-normal);

    &__icon {
      // -1px to avoid isue with razor thin white line between icon container and notification border
      border-top-left-radius: calc(
        var(--tui-border-radius-normal) - var(--tui-border-width-thin) - 1px
      );
      border-bottom-left-radius: calc(
        var(--tui-border-radius-normal) - var(--tui-border-width-thin) - 1px
      );
    }
  }
}
</style>
