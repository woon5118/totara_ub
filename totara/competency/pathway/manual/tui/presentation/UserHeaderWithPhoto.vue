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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-pathwayManual-userHeaderWithPhoto">
    <div class="tui-pathwayManual-userHeaderWithPhoto__photoContainer">
      <img
        :src="photoUrl"
        :alt="fullName"
        class="tui-pathwayManual-userHeaderWithPhoto__photoContainer_photo"
      />
    </div>
    <h2 class="tui-pathwayManual-userHeaderWithPhoto__title">
      {{ pageTitle }}
    </h2>
  </div>
</template>

<script>
export default {
  props: {
    pageTitle: {
      required: true,
      type: String,
    },
    photoUrl: {
      required: true,
      type: String,
    },
    fullName: {
      required: true,
      type: String,
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-userHeaderWithPhoto {
  display: table-row;

  &__photoContainer,
  &__title {
    display: table-cell;
  }

  &__photoContainer {
    max-width: 75px;
    vertical-align: middle;
    &_photo {
      width: 100%;
    }
  }

  &__title {
    margin: 0;
    padding-left: 1.5rem;
    vertical-align: middle;
  }
}
</style>
