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
  @package totara_core
-->

<template>
  <Card
    class="tui-actionCard"
    :class="[stacked && 'tui-actionCard--stacked']"
    :has-shadow="true"
  >
    <div class="tui-actionCard__body">
      <slot name="card-body" />
    </div>
    <div v-if="this.$slots['card-action']" class="tui-actionCard__action">
      <slot name="card-action" />
    </div>
  </Card>
</template>

<script>
import Card from 'totara_core/components/card/Card';

export default {
  components: {
    Card,
  },

  props: {
    stacked: Boolean,
  },
};
</script>
