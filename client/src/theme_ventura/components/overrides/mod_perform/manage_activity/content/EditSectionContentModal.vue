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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performEditSectionContentModal {
  &__title {
    margin-top: 0;
    @include tui-font-heading-medium;
  }

  &__required {
    color: var(--tui-color-prompt-alert);
  }

  &__form {
    & > * + * {
      margin-top: var(--tui-gap-4);
    }
  }
}
</style>
