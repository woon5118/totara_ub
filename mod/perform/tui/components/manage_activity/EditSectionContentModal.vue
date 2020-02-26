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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<template>
  <Modal size="sheet" :aria-labelledby="$id('title')">
    <ModalContent :title="'Title'" :title-id="$id('title')">
      <p>
        Content
      </p>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';

export default {
  components: {
    Modal,
    ModalContent,
  },
  data() {},
};
</script>

<lang-strings>
  {
    "mod_perform": []
  }
</lang-strings>
