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
  <Popover v-if="scale" :triggers="['click']">
    <template v-slot:trigger>
      <Button :styleclass="{ reveal: true }" :text="value.name" />
    </template>
    <RatingScaleOverview :scale="scale" :reverse-values="true" />
  </Popover>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Popover from 'totara_core/components/popover/Popover';
import RatingScaleOverview from 'totara_competency/components/RatingScaleOverview';

export default {
  components: {
    Button,
    Popover,
    RatingScaleOverview,
  },

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
