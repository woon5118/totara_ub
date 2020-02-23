<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
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
