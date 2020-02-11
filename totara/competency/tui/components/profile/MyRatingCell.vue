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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <Popover v-if="scale" :triggers="['hover', 'focus', 'click']">
    <template v-slot:trigger>
      <div class="tui-totaraCompetency__myRatingCell">{{ value.name }}</div>
    </template>
    <RatingScaleOverview :scale="scale" :reverse-values="true" />
  </Popover>
</template>

<script>
import Popover from 'totara_core/components/popover/Popover';
import RatingScaleOverview from 'totara_competency/components/RatingScaleOverview';

export default {
  components: { Popover, RatingScaleOverview },
  props: {
    value: {
      required: true,
      type: Object,
    },
    scales: {
      required: true,
      type: Array,
    },
  },

  computed: {
    scale() {
      return this.scales.find(({ id }) => id === this.value.scale_id);
    },
  },
};
</script>
<style lang="scss">
.tui-totaraCompetency__myRatingCell {
  display: inline-block;
  text-decoration: underline;
  text-decoration-style: dashed;
  cursor: pointer;
}
</style>
