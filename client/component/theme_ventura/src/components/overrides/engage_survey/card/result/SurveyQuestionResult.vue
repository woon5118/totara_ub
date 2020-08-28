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
.tui-surveyQuestionResult {
  &__progressBar {
    margin-bottom: var(--tui-gap-2);
  }

  &__progress {
    display: flex;
    align-items: center;
    margin-top: var(--tui-gap-4);
  }

  &__cardProgress {
    display: flex;
    align-items: center;
  }

  &__bar {
    width: 100%;
  }

  &__count {
    @include tui-font-body();
    margin-left: var(--tui-gap-2);
    text-align: end;
  }

  &__percent {
    @include tui-font-body();
    margin-right: var(--tui-gap-2);
    color: var(--tui-color-secondary);
    font-weight: bold;
  }

  &__answer {
    @include tui-font-body();
    -ms-word-break: break-all;
    overflow-wrap: break-word;
    hyphens: none;
  }

  &__votes {
    @include tui-font-heading-label();
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-4);
  }

  &__participant {
    @include tui-font-heavy();
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-2);
  }
}
</style>
