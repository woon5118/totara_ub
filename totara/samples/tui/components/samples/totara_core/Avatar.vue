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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    <div>
      <Avatar :src="url1" alt="theAlt1" size="large" />
      <Avatar :src="url1" alt="theAlt2" size="medium" />
      <Avatar :src="url1" alt="theAlt3" size="small" />
      <Avatar :src="url1" alt="theAlt4" size="xsmall" />
    </div>
    <br />
    <div>
      <Avatar :src="url2" alt="theAlt1" size="large" />
      <Avatar :src="url2" alt="theAlt2" size="medium" />
      <Avatar :src="url2" alt="theAlt3" size="small" />
      <Avatar :src="url2" alt="theAlt4" size="xsmall" />
    </div>
  </div>
</template>

<script>
import Avatar from 'totara_core/components/avatar/Avatar';

export default {
  components: {
    Avatar,
  },

  data() {
    return {
      url1: 'https://i.imgur.com/SnSRJUH.jpg',
      url2: 'https://i.imgur.com/14WAio1.jpg',
    };
  },
};
</script>
