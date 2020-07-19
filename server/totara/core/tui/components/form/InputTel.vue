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
  <Input v-bind="$props" type="tel" v-on="$listeners" />
</template>

<script>
// Components
import Input from 'totara_core/components/form/Input';

export default {
  components: {
    Input,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: [
    'ariaDescribedby',
    'ariaInvalid',
    'ariaLabel',
    'ariaLabelledby',
    'autocomplete',
    'autofocus',
    'dir',
    'disabled',
    'id',
    'list',
    'maxlength',
    'minlength',
    'name',
    'pattern',
    'placeholder',
    'readonly',
    'required',
    'size',
    'spellcheck',
    'styleclass',
    'value',
  ],
};
</script>
