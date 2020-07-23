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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-performUserActivitiesSelectParticipants {
  &__header {
    @include tui-font-heading-medium;
    padding-top: var(--tui-gap-2);
  }

  & > * + *,
  &__instance {
    margin-top: var(--tui-gap-4);
  }

  &__instance {
    display: block;
    padding: var(--tui-gap-4);
    border: var(--tui-border-width-thin) solid var(--tui-card-border-color);

    &-title {
      @include tui-font-heading-small;
      margin: 0;
    }

    &-meta {
      margin-top: var(--tui-gap-2);
    }

    &-form {
      margin-top: var(--tui-gap-4);
    }
  }
}
</style>
