<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_samples
-->

<template>
  <div class="tui-samplesExample">
    <Collapsible v-model="expanded" label="Example">
      <div class="tui-samplesExample__box">
        <slot />
      </div>
    </Collapsible>
  </div>
</template>

<script>
import Collapsible from 'totara_core/components/collapsible/Collapsible';

export default {
  components: {
    Collapsible,
  },

  data() {
    return {
      expanded: true,
    };
  },
};
</script>

<style lang="scss">
.tui-samplesExample {
  margin-top: var(--tui-gap-4);

  &__box {
    padding: var(--tui-gap-4);
  }
}
</style>
