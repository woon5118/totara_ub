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
    <Tag :text="text1">
      <template v-slot:button>
        <ButtonIcon
          :styleclass="{
            transparent: true,
            primary: true,
            small: true,
          }"
          aria-label="tag-remove"
          @click="$emit('remove')"
        >
          <Close size="100" />
        </ButtonIcon>
      </template>
    </Tag>

    <Tag :text="text2" />
    <Tag :text="text3" href="https://www.google.com" />
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Close from 'totara_core/components/icons/common/Close';
import Tag from 'totara_core/components/tag/Tag';

export default {
  components: {
    ButtonIcon,
    Close,
    Tag,
  },
  data() {
    return {
      text1: 'Wellington',
      text2: 'Auckland',
      text3: 'Google',
    };
  },
};
</script>
