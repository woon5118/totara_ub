<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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
  @package totara_competency
-->

<template>
  <div
    class="tui-progressCircle"
    :class="{
      'tui-progressCircle--complete': complete,
    }"
  >
    <!-- Proficiency progress circle -->
    <div class="tui-progressCircle__circle">
      <span class="tui-progressCircle__circle-text">
        {{
          $str('achieved_n_out_of_n', 'totara_competency', {
            current: completed,
            target: target,
          })
        }}
      </span>
    </div>

    <div class="tui-progressCircle__label">
      {{ $str('completed', 'totara_competency') }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    complete: {
      type: Boolean,
    },
    completed: {
      type: Number,
    },
    target: {
      type: Number,
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "achieved_n_out_of_n",
      "completed"
    ]
  }

</lang-strings>
