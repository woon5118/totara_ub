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
  <div class="tui-totaraCompetency-achievementDisplayHeader">
    <div class="tui-totaraCompetency-achievementDisplayHeader_title">
      {{ title }}
    </div>
    <HelpPopover v-if="helpText">
      {{ helpText }}
    </HelpPopover>
  </div>
</template>

<script>
import HelpPopover from 'totara_competency/components/HelpPopover';

export default {
  components: { HelpPopover },

  props: {
    title: {
      required: true,
      type: String,
    },
    helpText: {
      required: false,
      type: String,
    },
  },

  data() {
    return {
      showTooltip: false,
    };
  },
};
</script>

<style lang="scss">
.tui-totaraCompetency-achievementDisplayHeader {
  padding-bottom: var(--tui-gap-1);
  border-bottom: 1px solid var(--tui-color-neutral-5);

  &_title {
    display: inline-block;
    margin-top: auto;
    margin-bottom: auto;
    font-weight: bold;
    font-size: var(--tui-font-size-16);
    vertical-align: middle;
  }

  &_help {
    display: inline;
    margin-bottom: var(--tui-gap-4);
  }
}
</style>
