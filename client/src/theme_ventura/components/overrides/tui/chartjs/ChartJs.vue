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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
:root {
  // Chart default font size
  --tui-font-size-chart-default: 14; // It has to be without units
  --tui-font-size-chart-doughnut-label: 16; // It has to be without units
  --tui-font-size-chart-doughnut-percentage-label: 30; // It has to be without units
}

.tui-chartJs__header {
  @include tui-font-heading-small();
  padding-bottom: var(--tui-gap-7);
  overflow: hidden;
  white-space: nowrap;
  text-align: center;
  text-overflow: ellipsis;
}
</style>
