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
.tui-surveyResultBody {
  display: flex;
  flex: 1;
  flex-direction: column;
  width: 100%;
  padding: var(--tui-gap-2) var(--tui-gap-4);

  &__title.tui-formLabel {
    @include tui-font-heading-label;
    width: 100%;
    margin-right: 0;
    margin-bottom: var(--tui-gap-6);
    -ms-word-break: break-all;
    overflow-wrap: break-word;
    hyphens: none;
  }

  &__progress {
    flex-basis: 90%;
  }

  &__footer {
    display: flex;
    flex-basis: 10%;
    flex-direction: column;
    justify-content: flex-end;
  }

  &__container {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
  }

  &__text {
    @include tui-font-body-small();
  }

  &__icon {
    align-self: flex-end;
    margin-right: -5px;
  }
}
</style>
