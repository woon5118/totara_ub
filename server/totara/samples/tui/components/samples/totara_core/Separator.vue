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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package totara_samples
-->

<template>
  <div id="testSeparator">
    <Separator />
    <br />
    <Separator :thick="true" />
    <br />
    <Separator :spread="true" />
    <br />
    <Separator :thick="true" :spread="true" />
    <br />
    <Separator :thick="true" :spread="true">
      hello world
    </Separator>
    <Separator :thick="true" :spread="true">
      <img
        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mM03f3sPwAF2gLXke5VKgAAAABJRU5ErkJggg=="
        width="32"
        height="32"
      />
    </Separator>
  </div>
</template>

<script>
import Separator from 'totara_core/components/decor/Separator';
export default {
  components: {
    Separator,
  },
};
</script>
