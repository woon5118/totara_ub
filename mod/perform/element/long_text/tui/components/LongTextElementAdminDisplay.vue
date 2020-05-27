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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @package performelement_long_text
-->
<template>
  <ElementAdminDisplay
    :type="type"
    :title="title"
    :error="error"
    :is-required="isRequired"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
  />
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';

export default {
  components: {
    ElementAdminDisplay,
  },
  props: {
    title: String,
    type: Object,
    data: Object,
    isRequired: Boolean,
    error: String,
  },
};
</script>
