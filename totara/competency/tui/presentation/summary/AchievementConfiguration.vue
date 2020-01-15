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
  <div class="tui-competencySummaryAchievementConfiguration">
    <div class="tui-competencySummaryAchievementConfiguration__header">
      <div class="tui-competencySummaryAchievementConfiguration__header_title">
        {{ $str('achievementpaths', 'totara_competency') }}
      </div>
      <a
        :href="editUrl"
        class="tui-competencySummaryAchievementConfiguration__header_edit"
        :title="$str('edit', 'moodle')"
      >
        <FlexIcon icon="edit" size="200" :alt="$str('edit', 'moodle')" />
      </a>
    </div>

    <div
      v-if="!$apollo.loading && hasPathways"
      class="tui-competencySummaryAchievementConfiguration__response"
    >
      <div class="tui-competencySummaryAchievementConfiguration__aggregation">
        <Label :label="$str('overallratingcalc', 'totara_competency')" />
        <div
          class="tui-competencySummaryAchievementConfiguration__aggregation_title"
        >
          {{ achievementConfiguration.overall_aggregation.title }}
        </div>
      </div>

      <div
        v-for="pathGroup in pathGroups"
        :key="pathGroup.key"
        class="tui-competencySummaryAchievementConfiguration__pathgroup"
      >
        <div
          v-for="scaleValue in pathGroup.scaleValues"
          :key="scaleValue.value"
          class="tui-competencySummaryAchievementConfiguration__pathgroup_scalevalue"
        >
          <Label :label="scaleValue.value" />

          <div
            class="tui-competencySummaryAchievementConfiguration__pathgroup_scalevalue__paths"
          >
            <div
              v-for="(path, pathIdx) in scaleValue.paths"
              :key="path.id"
              class="tui-competencySummaryAchievementConfiguration__pathgroup_scalevalue__path"
            >
              <Divider v-if="pathIdx" label="OR" bordered />

              <div
                class="tui-competencySummaryAchievementConfiguration__pathgroup_scalevalue__path__criteria"
                :class="{ bordered: path.multiCriteria }"
              >
                <div
                  v-for="(criterion, criterionIdx) in path.criteria_summary"
                  :key="criterionIdx"
                  class="tui-competencySummaryAchievementConfiguration__pathgroup_scalevalue__path_criterion"
                >
                  <Divider v-if="criterionIdx" label="AND" />

                  <Label :label="criterion.item_type" />
                  <span v-if="criterion.item_aggregation"
                    >({{ criterion.item_aggregation }})</span
                  >
                  <div
                    v-for="(item, itemIdx) in criterion.items"
                    :key="itemIdx"
                  >
                    {{ item }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-else-if="!$apollo.loading"
      class="tui-competencySummaryAchievementConfiguration__noPaths"
    >
      {{ $str('nopaths', 'totara_competency') }}
    </div>
  </div>
</template>

<script>
// Import components
import Label from 'totara_core/components/form/Label';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import Divider from 'totara_competency/presentation/common/Divider';

// Import queries
import achievementConfigurationQuery from 'totara_competency/graphql/achievement_criteria';

export default {
  // Register required components
  components: {
    Label,
    FlexIcon,
    Divider,
  },

  props: {
    competencyId: {
      type: Number,
    },
  },

  data() {
    return {
      filterValue: '123',
    };
  },

  computed: {
    editUrl() {
      return this.$url('/totara/competency/competency_edit.php', {
        s: 'achievementpaths',
        id: this.competencyId,
      });
    },

    hasPathways() {
      return (
        this.achievementConfiguration.paths != null &&
        this.achievementConfiguration.paths.length > 0
      );
    },

    // Order paths by sortorder
    // Group paths - MULTIVALUE paths will always be in their own group
    //             - all SINGLEVALUE paths are placed in a single group
    pathGroups: function() {
      let sortedPaths = [...this.achievementConfiguration.paths].sort(
          (a, b) => a.sortorder - b.sortorder
        ),
        topGroups = [],
        singleValueGroup = null,
        bottomGroups = [];
      for (let idx in sortedPaths) {
        let path = Object.assign({}, sortedPaths[idx]);
        path.multiCriteria = path.criteria_summary.length > 1;

        // multi-value paths are always placed in their own group with scale value 'Any value'
        // All single-value paths are grouped in a single group
        if (path.classification === 'MULTIVALUE') {
          if (!singleValueGroup) {
            topGroups.push({
              key: 'group-' + idx,
              scaleValues: [
                {
                  value: this.$str('anyscalevalue', 'totara_competency'),
                  paths: [path],
                },
              ],
            });
          } else {
            bottomGroups.push({
              key: 'group-' + idx,
              scaleValues: [
                {
                  value: this.$str('anyscalevalue', 'totara_competency'),
                  paths: [path],
                },
              ],
            });
          }
        } else {
          if (!singleValueGroup) {
            singleValueGroup = {
              key: 'group-' + idx,
              scaleValues: [],
            };
          }

          let svIdx = singleValueGroup.scaleValues.findIndex(
            item => item.value === path.scale_value
          );
          if (svIdx === -1) {
            svIdx = singleValueGroup.scaleValues.length;
            singleValueGroup.scaleValues.push({
              value: path.scale_value,
              paths: [],
            });
          }

          singleValueGroup.scaleValues[svIdx].paths.push(path);
        }
      }

      // Now merge all 3 together and return
      return singleValueGroup
        ? [...topGroups, singleValueGroup, ...bottomGroups]
        : topGroups;
    },
  },

  apollo: {
    achievementConfiguration: {
      query: achievementConfigurationQuery,
      variables() {
        return {
          competency_id: this.competencyId,
          summarized: true,
        };
      },
      update: data => data.totara_competency_achievement_criteria,
    },
  },

  methods: {},
};
</script>

<style lang="scss">
.tui-competencySummaryAchievementConfiguration {
  padding-top: var(--tui-gap-4);
  &__header {
    margin-bottom: var(--tui-gap-2);
    padding-bottom: var(--tui-gap-1);
    border-bottom: 1px solid var(--tui-color-neutral-5);

    &_title {
      display: inline-block;
      margin-top: auto;
      margin-bottom: auto;
      margin-left: var(--tui-gap-2);
      font-weight: bold;
      font-size: var(--tui-font-size-18);
    }

    &_edit {
      float: right;
      margin-bottom: var(--tui-gap-4);
      padding-left: var(--tui-gap-2);
    }
  }

  &__noPaths {
    padding: var(--tui-font-size-8);
    font-style: italic;
  }

  &__aggregation {
    display: flex;
    flex-direction: row;

    &_title {
      margin-right: 0.4rem;
      margin-left: 1rem;
      padding-top: 0.4rem;
      line-height: 1.5;
    }
  }

  &__pathgroup {
    display: flex;
    flex-direction: column;
    width: 100%;
    margin-bottom: 0.5em;
    padding: 1em;
    border: 1px solid black;
    border-radius: 6px;

    &_scalevalue {
      display: flex;
      border-top: 1px solid grey;

      &:first-child {
        border: none;
      }

      .tui-formLabel {
        width: 20%;
        font-weight: normal;
        text-transform: uppercase;
      }

      &__paths {
        width: 80%;
      }

      &__path {
        &__criteria {
          .tui-formLabel {
            min-width: 10em;
            font-weight: bold;
            text-transform: none;
          }

          span {
            font-style: italic;
          }
        }

        &__criteria.bordered {
          margin-bottom: 0.5em;
          padding: 1em;
          border: 0.5px solid lightgrey;
          border-radius: 6px;
        }
      }
    }
  }
}
</style>

<lang-strings>
{
  "moodle": [
    "edit"
  ],
  "totara_competency": [
    "achievementpaths",
    "overallratingcalc",
    "anyscalevalue",
    "nopaths"
  ]
}
</lang-strings>
