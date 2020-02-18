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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @author Marco Song <marco.song@totaralearning.com>
  @package totara_criteria
-->

<template>
  <div>
    <Table
      v-if="hasCompetencies"
      :data="achievements.items"
      :expandable-rows="true"
    >
      <template v-slot:header-row="">
        <HeaderCell size="14">
          <h4>{{ $str('competencies', 'totara_hierarchy') }}</h4>
        </HeaderCell>
        <HeaderCell
          size="2"
          class="tui-totaraCriteria-competencyAchievementDisplay__progress"
        >
          <div>
            {{ achievedCompetencies }} / {{ numberOfRequiredCompetencies }}
          </div>
          <div v-if="achievements.aggregation_type === 2">
            {{
              $str(
                'required_only',
                'totara_criteria',
                achievements.required_items
              )
            }}
          </div>
        </HeaderCell>
      </template>
      <template v-slot:row="{ row, expand }">
        <Cell size="1" style="text-align: center;">
          <CheckSuccess v-if="row.value && row.value.proficient" size="300" />
        </Cell>

        <Cell size="13">
          <a href="#" @click.prevent="expand()">{{
            row.competency.fullname
          }}</a>
        </Cell>
        <Cell size="2">
          <span v-if="row.value" v-text="row.value.name" />
        </Cell>
      </template>
      <template v-slot:expand-content="{ row }">
        <h4>{{ row.competency.fullname }}</h4>
        <p
          class="tui-totaraCriteria-competencyAchievementDisplay__summary"
          v-html="row.competency.description"
        />
        <div v-if="row.assigned">
          <a
            :href="
              $url('/totara/competency/profile/details/', {
                competency_id: row.competency.id,
                user_id: userId,
              })
            "
            class="btn btn-primary"
          >
            {{ $str('view_competency', 'totara_criteria') }}
          </a>
        </div>
        <div v-else-if="row.self_assignable">
          <a
            href="#"
            class="btn btn-primary"
            @click.prevent="showModal(row.competency)"
          >
            {{
              $str(
                achievements.current_user
                  ? 'self_assign_competency'
                  : 'assign_competency',
                'totara_criteria'
              )
            }}
          </a>
        </div>

        <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
          <ConfirmModal
            :modal-body="getModalBody(row.competency.fullname)"
            :title="getModalTitle()"
            @modalConfirm="assignCompetency(row.competency)"
            @modalDismiss="modalRequestClose"
          />
        </ModalPresenter>
      </template>
    </Table>
    <div v-else-if="!$apollo.loading">
      <h4>{{ $str('competencies', 'totara_hierarchy') }}</h4>
      <p>{{ noCompetencyMsg }}</p>
    </div>
  </div>
</template>

<script>
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Cell from 'totara_core/components/datatable/Cell';
import CheckSuccess from 'totara_core/components/icons/common/CheckSuccess';
import Table from 'totara_core/components/datatable/Table';

import CreateUserAssignmentMutation from '../../../competency/webapi/ajax/create_user_assignments.graphql';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import ConfirmModal from 'totara_criteria/components/ConfirmModal';

export default {
  components: {
    CheckSuccess,
    Cell,
    HeaderCell,
    Table,
    ModalPresenter,
    ConfirmModal,
  },

  props: {
    achievements: {
      required: true,
      type: Object,
    },
    noCompetencyMsg: {
      required: true,
      type: String,
    },
    userId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      modalOpen: false,
    };
  },

  computed: {
    achievedCompetencies() {
      return this.achievements.items.reduce((total, current) => {
        if (current.value && current.value.proficient) {
          total += 1;
        }

        return total;
      }, 0);
    },

    hasCompetencies() {
      return this.achievements.items.length > 0;
    },

    numberOfRequiredCompetencies() {
      if (this.achievements.aggregation_method === 1) {
        return this.achievements.items.length;
      } else {
        return this.achievements.required_items;
      }
    },
  },

  methods: {
    assignCompetency(competency) {
      this.$apollo
        .mutate({
          // Query
          mutation: CreateUserAssignmentMutation,
          // Parameters
          variables: {
            user_id: this.userId,
            competency_ids: [competency.id],
          },
        })
        .then(({ data }) => {
          if (data && data.totara_competency_create_user_assignments) {
            let result = data.totara_competency_create_user_assignments;
            if (result.length > 0) {
              this.$emit('self-assigned');
            }
            // TODO Handle case when no result is returned
          }
        })
        .catch(error => {
          alert('Unfortunately there was an error assigning competency');
          console.log('error');
          console.error(error);
        })
        .finally(() => this.modalRequestClose());
    },

    showModal() {
      this.modalOpen = true;
    },

    modalRequestClose() {
      this.modalOpen = false;
    },

    getModalBody(fullName) {
      return this.$str(
        'confirm_assign_competency_body',
        'totara_criteria',
        fullName
      );
    },

    getModalTitle() {
      return this.$str('confirm_assign_competency_title', 'totara_criteria');
    },
  },
};
</script>
<style lang="scss">
.tui-totaraCriteria-competencyAchievementDisplay {
  &__summary {
    margin-top: 10px;
    margin-bottom: 30px;
  }

  &__progress {
    text-align: right;
  }
}
</style>
<lang-strings>
  {
    "totara_criteria": [
      "assign_competency",
      "confirm_assign_competency_body",
      "confirm_assign_competency_title",
      "required_only",
      "self_assign_competency",
      "view_competency"
    ],
    "totara_hierarchy": [
      "competencies"
    ]
  }
</lang-strings>
