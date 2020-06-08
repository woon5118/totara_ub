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

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    <p>
      This toggle button is used to immediately update parts of the UI. It's an
      on/off switch for a state, and shouldn't be used as part of a form (used a
      checkbox instead).
    </p>

    <p>
      On <code>click</code> we change only the button's
      (<code>aria-pressed</code>) state, not its label.
    </p>

    <SamplesExample>
      <ToggleButton :text="text" :toggle-first="toggleFirst">
        <template v-slot:icon>
          <InfoIconButton :aria-label="'Help'">
            Some content here...
          </InfoIconButton>
        </template>
      </ToggleButton>

      <br /><br />

      <ToggleButton :text="text" :toggle-first="toggleFirst" />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="toggleFirst">
        <RadioGroup v-model="toggleFirst" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
    </SamplesPropCtl>
  </div>
</template>

<script>
// Components
import ToggleButton from 'totara_core/components/buttons/ToggleButton';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    ToggleButton,
    InfoIconButton,
    FormRow,
    Radio,
    RadioGroup,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      text: 'Toggle something in the UI',
      toggleFirst: true,
    };
  },
};
</script>
