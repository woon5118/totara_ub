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
  <FormRow>
    <SubmitCancelGroup
      :submitting="submitting"
      @submit="submit"
      @cancel="cancel"
    />
  </FormRow>
</template>

<script>
// Components
import SubmitCancelGroup from 'totara_core/components/buttons/SubmitCancelGroup';
import FormRow from 'totara_core/components/form/FormRow';

export default {
  components: {
    SubmitCancelGroup,
    FormRow,
  },

  props: {
    submitting: Boolean,
  },

  methods: {
    cancel() {
      this.$emit('cancel');
    },

    submit(e) {
      this.$emit('submit', e);
    },
  },
};
</script>
