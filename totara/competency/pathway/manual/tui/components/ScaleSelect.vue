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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @package pathway_manual
-->

<template>
  <Select
    v-if="scale"
    :id="selectId"
    :options="scaleValues"
    @input="selectInput"
  />
</template>

<script>
import Select from 'totara_core/presentation/form/Select';
export default {
  components: { Select },
  props: {
    competencyId: {
      required: true,
      type: Number,
    },
    scale: {
      required: true,
      type: Object,
    },
  },

  computed: {
    scaleValues() {
      return this.makeSelectOptions(this.scale.values);
    },
    selectId() {
      return 'comp_' + this.competencyId;
    },
  },

  methods: {
    makeSelectOptions(scaleValues) {
      var emptyOption = [{ label: '', id: -2 }];
      var scaleOptions = scaleValues.map(function(scaleValue) {
        return { label: scaleValue.name, id: scaleValue.id };
      });
      var noneOption = [
        {
          // Divider line above None-option
          label: '------------------',
          id: -1,
          disabled: true,
        },
        {
          label: this.$str('rating_none', 'totara_competency'),
          id: -1,
        },
      ];
      return emptyOption.concat(scaleOptions).concat(noneOption);
    },
    selectInput(value) {
      this.$emit('input', value);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "rating_none"
    ]
  }
</lang-strings>
