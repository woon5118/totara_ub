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
  <div>
    <MultiSelect v-model="selection" :options="options" :title="'temp title'" />
    <br />
    {{ selection }}
  </div>
</template>

<script>
import MultiSelect from 'totara_core/components/filters/MultiSelectFilter';

export default {
  components: {
    MultiSelect,
  },

  data() {
    return {
      options: [
        {
          id: 'course',
          label: 'Include courses',
        },
        {
          id: 'program',
          label: 'Include programs',
        },
        {
          id: 'user',
          label: 'Include users',
        },
      ],
      selection: ['program'],
    };
  },

  methods: {},
};
</script>
