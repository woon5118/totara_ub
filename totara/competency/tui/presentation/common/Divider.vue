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

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-totara-competency-divider">
    <div
      class="tui-totara-competency-divider__label"
      :class="{ bordered: bordered }"
    >
      {{ label }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
    },
    bordered: {
      type: Boolean,
    },
  },
};
</script>

<style lang="scss">
.tui-totara-competency-divider {
  border-top: 1px solid #d9d9d9;
  left: -0.4rem;
  margin-top: 4rem;
  padding: 1.6rem 0 0.8rem;
  position: relative;
  width: 50%;

  &__label {
    background: #fff;
    color: #daebd3;
    display: inline-block;
    left: 50%;
    padding: 0.4rem 0.8rem;
    position: absolute;
    text-transform: uppercase;
    top: -1.6rem;
    transform: translateX(-50%);

    &.bordered {
      background: #f5f5f5;
      border: 1px solid #d9d9d9;
      color: black;
    }
  }
}
</style>
