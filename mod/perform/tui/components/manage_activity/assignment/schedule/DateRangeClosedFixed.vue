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
  <div class="tui-PerformScheduleDataRangeClosedFixed">
    <FormScope :path="path">
      <FormRow
        :label="
          $str('schedule_activity_instance_creation_period', 'mod_perform')
        "
      >
        <Grid>
          <GridItem :units="1">{{
            $str('schedule_date_range_from', 'mod_perform')
          }}</GridItem>
          <GridItem :units="5"><FormText name="closedFrom"/></GridItem>
          <GridItem :units="1">{{
            $str('schedule_date_range_to', 'mod_perform')
          }}</GridItem>
          <GridItem :units="5"><FormText name="closedTo"/></GridItem>
        </Grid>
      </FormRow>
    </FormScope>
  </div>
</template>
<script>
import FormScope from 'totara_core/components/reform/FormScope';
import { FormRow, FormText } from 'totara_core/components/uniform';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

export default {
  components: {
    FormScope,
    FormRow,
    FormText,
    Grid,
    GridItem,
  },
  props: {
    path: [String, Array],
    error: String,
  },
};
</script>
<lang-strings>
  {
  "mod_perform" : [
    "schedule_activity_instance_creation_period",
    "schedule_date_range_from",
    "schedule_date_range_to"
  ]
  }
</lang-strings>
