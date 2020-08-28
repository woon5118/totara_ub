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
  --tui-survey-header-iconBtn-height: 3.2rem;
}

.tui-surveyVoteTitle {
  display: flex;
  flex-direction: column;

  &__head {
    display: flex;
    justify-content: space-between;

    .tui-iconBtn--circle {
      height: var(--tui-survey-header-iconBtn-height);
      .tui-iconBtn__icon {
        top: 0;
      }
    }

    > :last-child {
      margin-left: var(--tui-gap-2);
    }

    &__title {
      @include tui-font-heading-medium();
      width: 90%;
      margin: 0;
      -ms-word-break: break-all;
      overflow-wrap: break-word;
      hyphens: none;

      @media (max-width: $tui-screen-sm) {
        width: 100%;
      }
    }
  }
}
</style>
