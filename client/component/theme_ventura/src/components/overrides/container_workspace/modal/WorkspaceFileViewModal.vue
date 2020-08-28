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

  @author Qingyang Liu <qingyang liu@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
:root {
  --tui-workspaceFileViewModal-minHeight: 240px;
  --tui-workspaceFileViewModal-maxHeight: 500px;
}
.tui-workspaceFileViewModal {
  display: flex;
  flex-direction: column;

  &__header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    margin-bottom: var(--tui-gap-4);
  }

  &__title {
    @include tui-font-heading-label();
    width: 60%;
    padding-right: var(--tui-gap-4);
  }

  &__links {
    @include tui-font-body();
    display: flex;
    justify-content: space-between;
    width: 40%;
  }

  &__link {
    padding-right: var(--tui-gap-2);
  }

  &__area {
    @include tui-font-body();
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: var(--tui-workspaceFileViewModal-minHeight);
    border: 1px solid var(--tui-filter-bar-border-color);

    &__icon {
      margin-bottom: var(--tui-gap-8);
      color: var(--tui-color-neutral-6);
      font-size: 60px;
    }
  }

  &__media {
    display: inline-block;
    width: 100%;
    height: auto;
    min-height: var(--tui-workspaceFileViewModal-minHeight);
    max-height: var(--tui-workspaceFileViewModal-maxHeight);
  }

  &__media.tui-videoBlock {
    margin: 0;
  }

  &__audio {
    display: inline-block;
    width: 100%;

    &:focus {
      outline: none;
    }
  }
}
</style>
