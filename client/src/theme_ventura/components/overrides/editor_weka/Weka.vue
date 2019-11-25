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
  --tui-weka-select-color: #8cf;
}

.tui-weka {
  width: 100%;
  background-color: var(--tui-color-neutral-1);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  &__placeholder {
    // Styling for the place holder.
    &:before {
      color: var(--tui-color-neutral-6);
      content: attr(data-placeholder);
    }
  }

  .ProseMirror-focused {
    .tui-weka {
      &__placeholder {
        &:before {
          content: '';
        }
      }
    }
  }

  .ProseMirror {
    position: relative;
    padding: var(--tui-gap-4);
    white-space: pre-wrap;
    white-space: break-spaces;
    word-wrap: break-word;
    -webkit-font-variant-ligatures: none;
    font-variant-ligatures: none;
    font-feature-settings: 'liga' 0; /* the above doesn't seem to work in Edge */

    &:focus {
      outline: none;
    }

    hr {
      margin: 0 0 var(--tui-gap-2) 0;
    }

    pre {
      white-space: pre-wrap;
    }

    ol,
    ul {
      margin: 0;
      padding-left: var(--tui-gap-4);
    }

    ul ul {
      list-style-type: circle;
    }
    ul ul ul {
      list-style-type: square;
    }

    li {
      position: relative;
    }
  }

  .ProseMirror-hideselection *::selection,
  .ProseMirror-hideselection *::-moz-selection {
    background: transparent;
  }

  .ProseMirror-hideselection {
    caret-color: transparent;
  }

  .ProseMirror-selectednode {
    outline: var(--tui-border-width-normal) solid var(--tui-weka-select-color);
  }

  /* Make sure li selections wrap around markers */

  li.ProseMirror-selectednode {
    outline: none;
  }

  li.ProseMirror-selectednode:after {
    position: absolute;
    top: -2px;
    right: -2px;
    bottom: -2px;
    left: -32px;
    border: var(--tui-border-width-normal) solid var(--tui-weka-select-color);
    content: '';
    pointer-events: none;
  }

  .ProseMirror-gapcursor:before {
    // insert an nbsp to make gapcursor expand to full line height
    content: '\00a0';
  }
}
</style>
