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
:root {
  --tui-totaraComment-sidePanel-commentBox-height: 40vh;
}

.tui-sidePanelCommentBox {
  // Overriding the comment box to make it fit with the sidepanel.
  &__header {
    margin: 0;
    margin-bottom: var(--tui-gap-4);
    padding: 0;
    padding-bottom: var(--tui-gap-2);
    font-size: var(--tui-font-size-14);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-primary);
  }

  &__box {
    .tui-commentBox {
      // Overriding the comment thread to make it fit with the sidepanel
      &__comments {
        // This is needed for scrolling
        position: relative;

        height: var(--tui-totaraComment-sidePanel-commentBox-height);
        overflow: auto;

        // This is not support by IE or Edge.
        scroll-behavior: smooth;

        .tui-commentThread {
          &__comments {
            &__comment {
              &:not(:first-child) {
                padding-top: var(--tui-gap-4);
                border-top: var(--tui-border-width-thin) solid
                  var(--tui-color-neutral-5);
              }
            }
          }
        }
      }

      &__commentForm {
        padding-top: var(--tui-gap-4);
        border-top: var(--tui-border-width-thin) solid var(--tui-color-primary);
      }
    }
  }
}
</style>
