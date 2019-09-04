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
  position: relative;
  left: -0.4rem;
  width: 50%;
  margin-top: 4rem;
  padding: 1.6rem 0 0.8rem;
  border-top: 1px solid #d9d9d9;

  &__label {
    position: absolute;
    top: -1.6rem;
    left: 50%;
    display: inline-block;
    padding: 0.4rem 0.8rem;
    color: #daebd3;
    text-transform: uppercase;
    background: #fff;
    transform: translateX(-50%);

    &.bordered {
      color: black;
      background: #f5f5f5;
      border: 1px solid #d9d9d9;
    }
  }
}
</style>
