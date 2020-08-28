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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-linkBlock {
  position: relative;
  display: flex;
  max-width: 28.6rem;
  height: calc(7.6rem + 2px);
  font-size: var(--tui-font-size-13);
  line-height: 1.15;
  white-space: normal;
  border: 1px solid var(--tui-card-border-color);
  border-radius: 4px;
  transition: box-shadow var(--tui-transition-form-function)
    var(--tui-transition-form-duration);

  &:hover,
  &:focus {
    box-shadow: var(--tui-shadow-2);
  }

  &__image {
    flex-shrink: 0;
    order: 2;
    width: 6rem;
    height: 6rem;
    margin: var(--tui-gap-2);
    background-position: center;
    background-size: cover;
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;

    > img {
      width: 100%;
    }
  }

  &__summary {
    flex-grow: 1;
    order: 1;
    padding: var(--tui-gap-2);
    overflow: hidden;
  }

  &__site {
    margin-bottom: 0.3rem;
    font-weight: bold;
    font-size: var(--tui-font-size-12);
    line-height: 1.15;
  }

  &__title {
    margin-bottom: 0.3rem;
    color: var(--tui-color-state);
    font-weight: bold;
  }

  &__description {
    // show a max of 2 lines
    height: calc(var(--tui-font-size-13) * 2 * 1.15);
    overflow: hidden;
  }

  &__linkOnly {
    // show a max of 4 lines
    height: calc(var(--tui-font-size-13) * 4 * 1.12);
    margin-top: 0.2rem;
    overflow: hidden;
    color: var(--tui-color-state);
    font-weight: bold;
    font-size: var(--tui-font-size-13);
    line-height: 1.15;
  }

  &__overlayLink {
    position: absolute;
    // -1px to account for border
    top: -1px;
    right: -1px;
    bottom: -1px;
    left: -1px;
  }
}
</style>
