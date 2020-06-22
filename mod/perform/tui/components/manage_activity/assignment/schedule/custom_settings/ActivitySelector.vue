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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->

<template>
  <div>
    <FormScope path="dynamicCustomSettings">
      <FormSelect
        :id="$id('relative-date-reference-date')"
        name="activity"
        :validations="v => [v.required()]"
        :options="activityOptions"
      />
    </FormScope>
  </div>
</template>
<script>
import { FormSelect, FormScope } from 'totara_core/components/uniform';

import performActivitiesQuery from 'mod_perform/graphql/activities';

export default {
  components: { FormSelect, FormScope },
  props: {
    data: {
      type: Object,
      required: true,
    },
    configData: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      activities: [],
    };
  },
  computed: {
    activityOptions() {
      let defaultOption = {
        id: null,
        label: this.$str(
          'schedule_dynamic_another_activity_select',
          'mod_perform'
        ),
      };
      let option = this.activities
        .filter(item => item.id != this.configData.activityId)
        .map(item => {
          return {
            id: item.id,
            label: item.name,
          };
        });
      option.unshift(defaultOption);
      return option;
    },
  },
  apollo: {
    activities: {
      query: performActivitiesQuery,
      update: data => data.mod_perform_activities,
    },
  },
};
</script>

<lang-strings>
  {
  "mod_perform": [
    "schedule_dynamic_another_activity_select"
  ]
  }
</lang-strings>
