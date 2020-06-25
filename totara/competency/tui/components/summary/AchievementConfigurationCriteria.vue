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

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @package totara_competency
-->

<template>
  <Card :no-border="!path.multiCriteria">
    <div
      class="tui-competencySummaryAchievementCriteria"
      :class="{
        'tui-competencySummaryAchievementCriteria--multi': path.multiCriteria,
      }"
    >
      <template v-for="(criterion, criterionIdx) in path.criteria_summary">
        <Separator
          v-if="criterionIdx"
          :key="criterionIdx + 'and'"
          class="tui-competencySummaryAchievementCriteria__and"
        >
          <AndBox />
        </Separator>

        <div
          :key="criterionIdx"
          class="tui-competencySummaryAchievementCriteria__criterion"
        >
          <div>
            <h5
              class="tui-competencySummaryAchievementCriteria__criterion-header"
            >
              {{ criterion.item_type }}
            </h5>

            <span
              v-if="criterion.item_aggregation"
              class="tui-competencySummaryAchievementCriteria__criterion-aggregation"
            >
              ({{ criterion.item_aggregation }})
            </span>
          </div>

          <div
            class="tui-competencySummaryAchievementCriteria__criterion-items"
          >
            <div v-for="(item, itemIdx) in criterion.items" :key="itemIdx">
              {{ item.description }}
              <span v-if="item.error">
                <WarningIcon :size="200" />
                {{ item.error }}
              </span>
            </div>
          </div>
        </div>
      </template>
    </div>
  </Card>
</template>

<script>
// Components
import AndBox from 'totara_core/components/decor/AndBox';
import Card from 'totara_core/components/card/Card';
import Separator from 'totara_core/components/decor/Separator';
import WarningIcon from 'totara_core/components/icons/common/Warning';

export default {
  components: {
    AndBox,
    Card,
    Separator,
    WarningIcon,
  },

  props: {
    path: {
      type: Object,
    },
  },
};
</script>
