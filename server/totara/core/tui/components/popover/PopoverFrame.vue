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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-popoverFrame" :class="['tui-popoverFrame--' + side]">
    <Arrow :relative-side="side" :distance="arrowDistance" />
    <CloseButton class="tui-popoverFrame__close" @click="$emit('close')" />
    <div v-if="title" class="tui-popoverFrame__title">
      {{ title }}
    </div>
    <div class="tui-popoverFrame__content">
      <slot />
    </div>
    <div v-if="$scopedSlots.buttons" class="tui-popoverFrame__buttons">
      <slot name="buttons" />
    </div>
  </div>
</template>

<script>
import Arrow from 'totara_core/components/decor/Arrow';
import CloseButton from 'totara_core/components/buttons/CloseIcon';

export default {
  components: {
    Arrow,
    CloseButton,
  },

  props: {
    title: String,
    side: String,
    arrowDistance: Number,
  },
};
</script>
