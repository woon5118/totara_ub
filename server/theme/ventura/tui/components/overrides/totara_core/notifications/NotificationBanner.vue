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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
@mixin tui-notification-banner-color($name, $color) {
  .tui-notificationBanner {
    &--#{$name} {
      border-color: $color;
    }

    &--#{$name} &__icon {
      background: $color;
    }
  }
}

.tui-notificationBanner {
  $mod-toast: #{&}--toast;

  @include tui-font-body-small();

  display: flex;
  background-color: var(--tui-color-background);
  border: var(--tui-border-width-thin) solid var(--tui-color-prompt-info);
  border-radius: var(--tui-border-radius-small);

  &__icon {
    display: flex;
    padding: var(--tui-gap-4);
    color: var(--tui-color-neutral-1);
    background: var(--tui-color-prompt-info);
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
    padding: var(--tui-gap-4) var(--tui-gap-3);

    #{$mod-toast} & {
      padding: var(--tui-gap-3);
    }
  }

  &__dismiss {
    display: flex;

    &_button {
      color: var(--tui-color-neutral-6);
    }
  }
}

@include tui-notification-banner-color(
  'success',
  var(--tui-color-prompt-success)
);
@include tui-notification-banner-color(
  'warning',
  var(--tui-color-prompt-warning)
);
@include tui-notification-banner-color('error', var(--tui-color-prompt-alert));

@media screen and (min-width: $tui-screen-sm) {
  .tui-notificationBanner {
    @include tui-font-body();

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
