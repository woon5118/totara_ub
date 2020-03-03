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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    <div class="tui-testProgressBar">
      <Progress :value="count" />
      <Button
        text="Set"
        :styleclass="{ small: true }"
        @click="count = Math.random() * 100"
      />
      <br />
      <Progress :value="3" />
      <br />
      <Progress :value="25" />
      <br />
      <Progress :value="50" :hide-value="true" />
      <br />
      <Progress :value="74" format="number" />
      <br />
      <Progress :value="96" :hide-value="true" :small="true" />
      <br />
      <Progress :value="100" />
      <br />
      <Progress :value="100" :completed-text="true" />
    </div>
    <br />
    <div>
      <Progress :value="1" />
      <br />
      <Progress :value="2" />
      <br />
      <Progress :value="3" />
      <br />
      <Progress :value="5" />
      <br />
      <Progress :value="25" :min="10" :max="50" format="number" />
      <br />
      <Progress :value="25" :min="10" :max="50" />
      <br />
      <Progress :value="50" :hide-value="true" />
      <br />
      <Progress :value="96" :hide-value="true" :small="true" />
      <br />
      <Progress :value="100" />
      <br />
      <Progress :value="100" :completed-text="true" />
    </div>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Progress from 'totara_core/components/progress/Progress';

export default {
  components: {
    Button,
    Progress,
  },
  data() {
    return {
      count: 0,
    };
  },
};
</script>

<style lang="scss">
.tui-testProgressBar {
  width: 400px;
  padding: 1rem;
  background-color: #767676;
}
</style>
