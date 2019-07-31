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
:root {
  --tui-engageSurvey-badge-height: 40px;
  --tui-engageSurvey-badge-width: 105px;
}

.tui-surveyBadge {
  width: var(--tui-engageSurvey-badge-width);
  height: var(--tui-engageSurvey-badge-height);

  &__text {
    font-weight: bold;
    font-size: 13px;
    fill: #262626;
  }

  &__shapeParent {
    fill: none;
    fill-rule: evenodd;
    stroke: none;
    stroke-width: 1;
  }

  &__shape {
    fill: var(--tui-color-neutral-1);
    stroke: var(--tui-color-primary);
    stroke-width: 3;
  }
}
</style>
