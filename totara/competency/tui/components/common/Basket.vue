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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package totara_competency
-->

<template>
  <Card class="tui-totaraCompetencyBasket">
    <div class="tui-totaraCompetencyBasket__flex-item">
      <span class="bold">
        {{ $str('selected_items_label', 'totara_competency', items.length) }}
      </span>
    </div>
    <div
      v-if="items.length > 0 && isViewingSelections"
      class="tui-totaraCompetencyBasket__flex-item"
    >
      <a
        class="tui-totaraCompetencyBasket__flex-item"
        href="#"
        @click.prevent="$emit('clearAll')"
      >
        {{ $str('clearall', 'totara_core') }}
      </a>
    </div>
    <div
      v-if="isViewingSelections"
      class="tui-totaraCompetencyBasket__flex-item tui-totaraCompetencyBasket__flex-item--own-line-sm tui-totaraCompetencyBasket__flex-item--push-right-md"
    >
      <a href="#" @click.prevent="$emit('goBackToAll')">
        {{ goBackText }}
      </a>
    </div>
    <div
      v-else-if="hasSelectedItems"
      class="tui-totaraCompetencyBasket__flex-item tui-totaraCompetencyBasket__flex-item--push-right-md"
    >
      <a href="#" @click.prevent="$emit('viewSelections')">
        {{ $str('viewselected', 'totara_core') }}
      </a>
    </div>
    <div
      class="tui-totaraCompetencyBasket__flex-item tui-totaraCompetencyBasket__flex-item--primary"
    >
      <Button
        class="tui-totaraCompetencyBasket__primary-button"
        :disabled="!hasSelectedItems"
        :text="mainActionText"
        @click="$emit('mainAction')"
      />
    </div>
  </Card>
</template>

<script>
import Card from 'totara_core/components/card/Card';
import Button from 'totara_core/components/buttons/Button';

export default {
  name: 'Basket',
  components: { Card, Button },
  props: {
    mainActionText: {
      type: String,
      required: true,
    },
    goBackText: {
      type: String,
      required: true,
    },
    items: {
      type: Array,
      required: true,
    },
    isViewingSelections: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    hasSelectedItems() {
      return this.items.length > 0;
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "clearall",
      "viewselected"
    ],
    "totara_competency": [
      "selected_items_label"
    ]
  }
</lang-strings>
