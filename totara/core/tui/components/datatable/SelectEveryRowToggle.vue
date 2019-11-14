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
  @package totara_core
-->

<template>
  <div class="tui-dataTableSelectEveryRowToggle">
    <div v-if="showSelect" name="selectEntirePrompt">
      {{ $str('selectallpageselected', 'totara_core', { count: count }) }}
      <a href="#" @click.prevent="selectFunc">
        {{ $str('selectentireresult', 'totara_core') }}
      </a>
    </div>

    <div v-else name="clearEntirePrompt">
      {{ $str('selectentireresultselected', 'totara_core') }}
      <a href="#" @click.prevent="clearFunc">
        {{ $str('clearselection', 'totara_core') }}
      </a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    clearFunc: Function,
    count: Number,
    selectFunc: Function,
    showSelect: Boolean,
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "clearselection",
    "selectallpageselected",
    "selectentireresult",
    "selectentireresultselected"
  ]
}
</lang-strings>
