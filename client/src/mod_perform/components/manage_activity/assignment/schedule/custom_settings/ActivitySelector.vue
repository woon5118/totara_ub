<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
-->
<template>
  <div>
    <Loader :loading="$apollo.loading">
      <FormScope path="dynamicCustomSettings">
        <FormSelect
          :id="$id('relative-date-reference-date')"
          :options="activityOptions"
          :validations="v => [v.required()]"
          name="activity"
        />
      </FormScope>
    </Loader>
  </div>
</template>

<script>
import { FormSelect, FormScope } from 'tui/components/uniform';
import performActivitiesQuery from 'mod_perform/graphql/activities';
import Loader from 'tui/components/loader/Loader';

export default {
  components: { Loader, FormSelect, FormScope },
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
        id: undefined, // This must be undefined so it will match the uniform default value.
        label: this.$str(
          'schedule_dynamic_another_activity_select',
          'mod_perform'
        ),
      };
      let options = this.activities
        .filter(item => item.id != this.configData.activityId)
        .map(item => {
          return {
            id: item.id,
            label: item.name,
          };
        });

      options.unshift(defaultOption);

      return options;
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
