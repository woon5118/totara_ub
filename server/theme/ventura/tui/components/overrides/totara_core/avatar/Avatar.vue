<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
:root {
  --tui-avatar-border-radius: 50%;
  --tui-avatar-large-width: 185px;
  --tui-avatar-large-height: 185px;
  --tui-avatar-large-padding: 5px;
  --tui-avatar-medium-width: 105px;
  --tui-avatar-medium-height: 105px;
  --tui-avatar-medium-padding: 3px;
  --tui-avatar-small-width: 50px;
  --tui-avatar-small-height: 50px;
  --tui-avatar-small-padding: 2px;
  --tui-avatar-xsmall-width: 38px;
  --tui-avatar-xsmall-height: 38px;
  --tui-avatar-xsmall-padding: 1px;
  --tui-avatar-xxsmall-width: 24px;
  --tui-avatar-xxsmall-height: 24px;
  --tui-avatar-xxsmall-padding: 1px;
}

.tui-avatar {
  border: var(--tui-border-width-normal) solid var(--tui-avatar-border-color);
  border-radius: var(--tui-avatar-border-radius);

  &--large {
    width: var(--tui-avatar-large-width);
    height: var(--tui-avatar-large-height);
    padding: var(--tui-avatar-large-padding);
  }

  &--medium {
    width: var(--tui-avatar-medium-width);
    height: var(--tui-avatar-medium-height);
    padding: var(--tui-avatar-medium-padding);
  }

  &--small {
    width: var(--tui-avatar-small-width);
    height: var(--tui-avatar-small-height);
    padding: var(--tui-avatar-small-padding);
  }

  &--xsmall {
    width: var(--tui-avatar-xsmall-width);
    height: var(--tui-avatar-xsmall-height);
    padding: var(--tui-avatar-xsmall-padding);
  }

  &--xxsmall {
    width: var(--tui-avatar-xxsmall-width);
    height: var(--tui-avatar-xxsmall-height);
    padding: var(--tui-avatar-xxsmall-padding);
  }
}
</style>
