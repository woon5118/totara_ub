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
  @package totara_samples
-->

<template>
  <div>
    Vertical tabs

    <SamplesExample>
      <Tabs direction="vertical">
        <Tab
          v-for="tab in tabs"
          :id="tab.id"
          :key="tab.id"
          :name="tab.name"
          :disabled="disabledId === tab.id"
        >
          {{ tab.content }}
        </Tab>
      </Tabs>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';

export default {
  components: {
    SamplesCode,
    SamplesExample,
    Tab,
    Tabs,
  },

  data() {
    const content = x => 'tab content ' + x;
    return {
      tabs: [
        { id: 1, name: 'Attendees', content: content(1) },
        { id: 2, name: 'Wait-list', content: content(2) },
        { id: 3, name: 'Cancellations', content: content(3) },
        { id: 4, name: 'Take attendance', content: content(4) },
        { id: 5, name: 'Message users', content: content(5) },
      ],
      selectedId: 1,
      disabledId: '2',
      codeTemplate: `<Tabs direction="vertical">
  <Tab
    v-for="tab in tabs"
    :id="tab.id"
    :key="tab.id"
    :name="tab.name"
    :disabled="tab.disabled"
  >
    {{ tab.content }}
  </Tab>
</Tabs>`,
      codeScript: `import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';

export default {
  components: {
    Tab,
    Tabs,
  }
}`,
    };
  },
};
</script>
