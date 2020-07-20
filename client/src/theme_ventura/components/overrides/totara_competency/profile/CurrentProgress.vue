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
.tui-competencyProfileCurrentProgress {
  margin-bottom: var(--tui-gap-8);

  &__progress {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0 0 var(--tui-gap-4) 0;
    padding: 0;
    list-style: none;

    @media (min-width: $tui-screen-xs) {
      justify-content: flex-start;
    }
  }

  &__userDetails {
    display: flex;
    flex-direction: column;

    & > :not(:last-child) {
      margin-bottom: var(--tui-gap-4);
    }
  }

  &__latestAchievement {
    $header-v-padding: var(--tui-gap-2);
    $header-height: calc(
      var(--tui-font-size-13) * 1.15 + var(--tui-border-width-normal) + #{$header-v-padding} *
        2
    );

    display: flex;
    flex-direction: column;
    margin-top: var(--tui-gap-10);
    padding: 0 var(--tui-gap-1);
    background: var(--tui-color-background);
    border: var(--tui-border-width-normal) solid var(--tui-color-primary);
    border-radius: 6px;

    &-header {
      max-width: 100%;
      height: $header-height;
      margin: calc(
          -1 * var(--tui-border-width-normal) - (#{$header-height} / 2)
        )
        auto calc(-1 * var(--tui-gap-2)) auto;
      padding: $header-v-padding var(--tui-gap-4);
      overflow: hidden;
      font-weight: bold;
      font-size: var(--tui-font-size-13);
      line-height: 1.15;
      white-space: nowrap;
      text-overflow: ellipsis;
      background: var(--tui-color-background);
      border: var(--tui-border-width-normal) solid var(--tui-color-primary);
      border-radius: 6px;
    }

    &-content {
      padding: var(--tui-gap-4) var(--tui-gap-6);
    }
  }
}
</style>
