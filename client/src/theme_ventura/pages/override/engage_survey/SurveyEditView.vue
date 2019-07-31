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

  @author Qingyang Liu <qingyang.liu@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
.tui-surveyEditView {
  .tui-grid-item {
    min-height: var(--tui-engageSurvey-min-height);
  }

  &__navBar {
    margin-top: var(--tui-gap-4);
    margin-left: var(--tui-gap-4);
  }

  &__layout {
    display: flex;
    &__content {
      display: flex;
      flex-direction: column;
      justify-content: center;
      margin: 0 auto;
      padding: 0 0 var(--tui-gap-12) 0;
      @media (max-width: $tui-screen-sm) {
        max-width: 80vw;
        margin-left: var(--tui-gap-8);
      }
    }
  }
}
</style>
