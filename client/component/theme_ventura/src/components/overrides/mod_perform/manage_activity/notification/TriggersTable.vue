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

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-activityNotificationsTriggersTable {
  width: 100%;
  &__banner {
    margin-top: var(--tui-gap-2);
  }
}
.tui-activityNotificationsTriggers {
  margin: 0;
  padding: 0;
  list-style: none;
}
.tui-activityNotificationsTrigger {
  $value-width: 6em;
  width: 100%;
  margin: 0 0 var(--tui-gap-2) 0;
  padding: 0;
  &__field {
    display: flex;
    flex-grow: 1;
    align-items: center;
  }
  &__value {
    width: $value-width;
  }
  &__format {
    width: calc(100% - #{$value-width} - var(--tui-gap-4));
    margin: 0 var(--tui-gap-2);
  }
  &__error {
    margin-top: var(--tui-gap-1);
  }
}
</style>
