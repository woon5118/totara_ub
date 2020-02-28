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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyDetailAssignment">
    <Grid :stack-at="700">
      <GridItem :units="4">
        <!-- Competency assignment select list -->
        <SelectFilter
          v-model="selectedAssignment"
          :label="$str('assignment', 'totara_competency')"
          :large="true"
          :options="activeAssignmentList"
          @input="input"
        />
      </GridItem>
      <GridItem :units="4" :class="'tui-competencyDetailAssignment__level'">
        <div
          class="tui-competencyDetailAssignment__level-wrap"
          :class="
            'tui-competencyDetailAssignment__level-wrap-' +
              selectedAssignmentProficiencyState
          "
        >
          <h5 class="tui-competencyDetailAssignment__level-header">
            {{ $str('achievement_level', 'totara_competency') }}
            <InfoIconButton
              :aria-label="$str('more_information', 'totara_competency')"
              :class="'tui-competencyDetailAssignment__level-infoBtn'"
            >
              ...
            </InfoIconButton>
          </h5>
          <div class="tui-competencyDetailAssignment__level-text">
            {{ selectedAssignmentProficiency.name }}
          </div>
        </div>
      </GridItem>
      <GridItem :units="4" :class="'tui-competencyDetailAssignment__status'">
        <ProgressTrackerCircle
          :state="selectedAssignmentProficiencyState"
          :target="selectedAssignmentProficiencyState !== 'complete'"
        />

        <span
          class="tui-competencyDetailAssignment__status-text"
          :class="{
            'tui-competencyDetailAssignment__status-text-complete':
              selectedAssignmentProficiencyState === 'complete',
          }"
        >
          {{
            $str(
              selectedAssignmentProficiency.proficient
                ? 'proficient'
                : 'not_proficient',
              'totara_competency'
            )
          }}
        </span>
      </GridItem>
    </Grid>
  </div>
</template>

<script>
// Components
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import ProgressTrackerCircle from 'totara_core/components/progresstracker/ProgressTrackerCircle';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    Grid,
    GridItem,
    InfoIconButton,
    ProgressTrackerCircle,
    SelectFilter,
  },

  props: {
    activeAssignmentList: {
      required: true,
      type: Array,
    },
    selectedAssignmentProficiency: {
      type: Object,
    },
    value: {
      type: Number,
    },
  },

  data() {
    return {
      selectedAssignment: this.value,
    };
  },

  computed: {
    /**
     * Return proficient state (pending, complete, achieved)
     *
     * @return {String}
     */
    selectedAssignmentProficiencyState() {
      if (
        this.selectedAssignmentProficiency.id &&
        this.selectedAssignmentProficiency.proficient
      ) {
        return 'achieved';
      } else if (this.selectedAssignmentProficiency.id) {
        return 'complete';
      } else {
        return 'pending';
      }
    },
  },

  methods: {
    input(e) {
      this.$emit('input', e);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "achievement_level",
      "assignment",
      "more_information",
      "not_proficient",
      "proficient"
    ]
  }
</lang-strings>
