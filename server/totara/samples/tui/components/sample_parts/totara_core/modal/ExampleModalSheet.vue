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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_samples
-->

<template>
  <Modal size="sheet" :aria-labelledby="$id('title')">
    <div>
      <h1 :id="$id('title')">
        Arbitrary content
      </h1>
      goes here
    </div>
  </Modal>
</template>

<script>
import Modal from 'totara_core/components/modal/Modal';

export default {
  components: {
    Modal,
  },
};
</script>
