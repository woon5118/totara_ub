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
  <Button
    :styleclass="{ primary: 'true' }"
    :text="$str('submit', 'moodle')"
    type="submit"
    @click="$emit('click', $event)"
  />
</template>

<script>
import Button from 'totara_core/components/buttons/Button';

export default {
  components: {
    Button,
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "submit"
  ]
}
</lang-strings>
