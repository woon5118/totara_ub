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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <Grid :stack-at="764">
    <GridItem :units="4">
      <div>
        {{ $str('section_element_summary_required_questions', 'mod_perform') }}
      </div>
      <div class="tui-performActivitySectionElementSummary__count">
        {{ elementsSummary.required_question_count }}
      </div>
    </GridItem>
    <GridItem :units="4">
      <div>
        {{ $str('section_element_summary_optional_questions', 'mod_perform') }}
      </div>
      <div class="tui-performActivitySectionElementSummary__count">
        {{ elementsSummary.optional_question_count }}
      </div>
    </GridItem>
    <GridItem :units="4">
      <div>
        {{
          $str('section_element_summary_other_content_elements', 'mod_perform')
        }}
      </div>
      <div class="tui-performActivitySectionElementSummary__count">
        {{ elementsSummary.other_element_count }}
      </div>
    </GridItem>
  </Grid>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

export default {
  components: {
    GridItem,
    Grid,
  },
  props: {
    elementsSummary: {
      type: Object,
      required: true,
    },
  },
};
</script>

<lang-strings>
  {
  "mod_perform": [
    "section_element_summary_required_questions",
    "section_element_summary_optional_questions",
    "section_element_summary_other_content_elements"
  ]
  }
</lang-strings>
