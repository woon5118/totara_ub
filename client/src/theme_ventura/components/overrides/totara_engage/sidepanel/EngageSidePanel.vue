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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-engageSidePanel {
  padding: var(--tui-gap-8);

  &__tabs {
    padding-top: var(--tui-gap-8);
    // Overriding the fallback select list when there isn't enough space
    .tui-formRow__action {
      width: 200px;
    }
  }

  &__commentBox {
    // Since the tab is already having a padding which it is '--tui-gap-4'.
    // Therefore we just need another '--tui-gap-4'.
    margin-top: var(--tui-gap-4);
  }

  &__overviewBox {
    // Since the tab is already having a padding which it is '--tui-gap-4'.
    // Therefore we just need another '--tui-gap-4'.
    margin-top: var(--tui-gap-4);
  }
}
</style>
