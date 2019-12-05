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
  <div class="tui-pathwayManual-frameworkGroup">
    <div
      class="tui-pathwayManual-frameworkGroup__titleBar"
      @click.prevent="toggleOpen"
    >
      <span class="tui-pathwayManual-frameworkGroup__titleBar_icon">
        <FlexIcon v-if="showContent" icon="nav-expanded" size="200" />
        <FlexIcon v-else icon="nav-expand" size="200" />
      </span>
      <span class="tui-pathwayManual-frameworkGroup__titleBar_title">
        {{ groupTitle }}
      </span>
    </div>
    <Table
      v-show="showContent"
      :data="group.competencies"
      :expandable-rows="false"
      class="tui-pathwayManual-frameworkGroup__table"
    >
      <template v-slot:header-row>
        <HeaderCell size="4">
          <strong>{{ $str('competency', 'totara_hierarchy') }}</strong>
        </HeaderCell>
        <HeaderCell size="1">
          <div class="tui-pathwayManual-frameworkGroup__table__block">
            <strong>{{ $str('last_rating_given', 'pathway_manual') }}</strong>
            <div
              class="tui-pathwayManual-frameworkGroup__table__help"
              @mouseover="showRatingTooltip = true"
              @mouseleave="showRatingTooltip = false"
            >
              <FlexIcon icon="info" size="200" />
              <Tooltip :display="showRatingTooltip">
                <span v-if="isForSelf">
                  {{ $str('last_rating_given_self_tooltip', 'pathway_manual') }}
                </span>
                <span v-else>
                  {{
                    $str('last_rating_given_other_tooltip', 'pathway_manual')
                  }}
                </span>
              </Tooltip>
            </div>
          </div>
        </HeaderCell>
        <HeaderCell
          size="2"
          class="tui-pathwayManual-frameworkGroup__table__block"
        >
          <div class="tui-pathwayManual-frameworkGroup__table__block">
            <strong>{{ $str('rate_competency', 'pathway_manual') }}</strong>
            <div
              class="tui-pathwayManual-frameworkGroup__table__help"
              @mouseover="showScaleTooltip = true"
              @mouseleave="showScaleTooltip = false"
            >
              <FlexIcon icon="info" size="200" />
              <ScaleTooltip
                :scale="group"
                :display="showScaleTooltip"
                :show-descriptions="true"
              />
            </div>
          </div>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row }">
        <Cell size="4">
          {{ row.competency.display_name }}
        </Cell>
        <Cell size="1">
          <span v-if="row.last_rating">
            <span v-if="row.last_rating.scale_value">
              {{ row.last_rating.scale_value.name }}
            </span>
            <span v-else>
              {{ $str('rating_set_to_none', 'pathway_manual') }}
            </span>
            <br />
            {{ row.last_rating.date }}<br />
            {{ getRater(row.last_rating.rater) }}
          </span>
        </Cell>
        <Cell size="2">
          <ScaleSelect
            :competency-id="parseInt(row.competency.id)"
            :scale="group"
            @input="value => selectCompValue(row.competency.id, value)"
          />
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
import Cell from 'totara_core/components/datatable/Cell';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import ScaleSelect from 'totara_competency/components/ScaleSelect';
import ScaleTooltip from 'totara_competency/components/ScaleTooltip';
import Table from 'totara_core/components/datatable/Table';
import Tooltip from 'totara_competency/components/Tooltip';

const ROLE_SELF = 'self';

export default {
  components: {
    Cell,
    FlexIcon,
    HeaderCell,
    ScaleSelect,
    ScaleTooltip,
    Table,
    Tooltip,
  },

  props: {
    currentUserId: {
      required: true,
      type: Number,
    },
    role: {
      required: true,
      type: String,
    },
    group: {
      required: true,
      type: Object,
    },
    expanded: {
      default: true,
      type: Boolean,
    },
  },

  data() {
    return {
      showContent: this.expanded,
      showRatingTooltip: false,
      showScaleTooltip: false,
    };
  },

  computed: {
    isForSelf() {
      return this.role === ROLE_SELF;
    },

    groupTitle() {
      let count = this.group.competencies.length;

      let string =
        count > 1
          ? 'competency_framework_count_plural'
          : 'competency_framework_count_singular';

      return this.$str(string, 'pathway_manual', {
        name: this.group.framework.display_name,
        count: count,
      });
    },
  },

  methods: {
    toggleOpen() {
      this.showContent = !this.showContent;
    },

    getRater(rater) {
      if (rater) {
        // Only display the rater's name if it's not the current user.
        return parseInt(rater.id) === this.currentUserId
          ? ''
          : '(' + rater.fullname + ')';
      } else {
        return this.$str('rater_details_removed', 'pathway_manual');
      }
    },

    selectCompValue(compId, value) {
      // TODO: Decide in TL-22009 if this should also be used for comments.
      this.$emit('input', {
        comp_id: compId,
        scale_value_id: value,
        comment: '',
      });
    },
  },
};
</script>

<style lang="scss">
.tui-pathwayManual-frameworkGroup {
  &:not(:last-child) {
    margin-bottom: var(--tui-gap-7);
  }

  &__titleBar {
    width: 100%;
    padding: var(--tui-gap-2) var(--tui-gap-3);
    background-color: var(--tui-color-neutral-2);
    cursor: pointer;

    &_title {
      font-weight: bold;
      font-size: var(--tui-font-size-16);
    }

    &_icon {
      color: var(--tui-color-theme-base);
      .flex-icon {
        vertical-align: text-top;
      }
    }
  }

  &__table {
    &__block {
      display: block;
    }

    &__help {
      display: inline;
    }
  }
}
</style>

<lang-strings>
  {
    "pathway_manual": [
      "competency_framework_count_plural",
      "competency_framework_count_singular",
      "last_rating_given",
      "last_rating_given_other_tooltip",
      "last_rating_given_self_tooltip",
      "rate_competency",
      "rater_details_removed",
      "rating_set_to_none"
    ],
    "totara_hierarchy": [
      "competency"
    ]
  }
</lang-strings>
