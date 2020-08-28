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

  @author Qingyang Liu <Qingyang.liu@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
.tui-surveyResultContent {
  &__participant {
    @include tui-font-body();
    @include tui-font-heavy();
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-2);
  }

  &__participantnumber {
    @include tui-font-body();
    @include tui-font-heavy();
    padding-right: var(--tui-gap-1);
  }
}
</style>
