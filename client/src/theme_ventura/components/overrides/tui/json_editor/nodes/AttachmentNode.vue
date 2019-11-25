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
.tui-attachmentNode {
  @media (max-width: 490px) {
    // From 490px downward
    width: 100%;
    overflow: hidden;
  }

  @media (min-width: 491px) {
    // From 490px onward
    flex-basis: 20%;
    min-width: 235px;
  }

  position: relative;
  display: flex;
  align-items: center;
  padding: var(--tui-gap-2);
  white-space: normal;
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: var(--tui-card-border-radius);

  &__info {
    width: 72%;

    @media (max-width: 490px) {
      width: 65%;
    }

    @media (max-width: 350px) {
      width: 50%;
    }

    &__fileSize {
      margin: 0;
      font-size: var(--tui-font-size-3);
      white-space: nowrap;
    }

    &__filename {
      position: relative;

      p {
        margin: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }

      &--truncate {
        &:after {
          position: absolute;
          top: 0;
          left: 100%;
          width: 100%;
          content: attr(data-file-extension);
        }
      }
    }
  }

  &__icon {
    flex-basis: 9%;
    margin-right: var(--tui-gap-2);
    color: var(--tui-color-state);
  }

  &--withDownloadUrl {
    cursor: pointer;
  }
}
</style>
