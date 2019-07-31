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

  @author Qingyang liu <qingyang.liu@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-playlistSidePanel {
  padding: var(--tui-gap-8);

  &__overview__timeDescription {
    @include tui-font-body-small();
  }

  &__overview__setting {
    display: flex;
    flex-direction: row;
  }

  &__overview__media {
    margin-top: 0;
    margin-left: var(--tui-gap-6);
  }
}
</style>
