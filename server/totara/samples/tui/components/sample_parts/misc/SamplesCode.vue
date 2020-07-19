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
  <div class="tui-samplesCode">
    <Collapsible v-model="expanded" label="Code example">
      <template v-if="$slots.template">
        <h5>Template</h5>
        <pre class="tui-samplesCode__box"><slot name="template" /></pre>
      </template>
      <template v-if="$slots.script">
        <h5>Script</h5>
        <pre class="tui-samplesCode__box"><slot name="script" /></pre>
      </template>
      <template v-if="$slots.style">
        <h5>Style</h5>
        <pre class="tui-samplesCode__box"><slot name="style" /></pre>
      </template>
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
      expanded: false,
    };
  },
};
</script>

<style lang="scss">
.tui-samplesCode {
  &__box {
    margin: var(--tui-gap-4) 0;
    padding: var(--tui-gap-4);
    background-color: var(--tui-color-neutral-3);
    border: 1px solid var(--tui-color-neutral-5);
  }
}
</style>
