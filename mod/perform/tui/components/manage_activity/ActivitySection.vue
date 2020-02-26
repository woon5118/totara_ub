<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theN
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package totara_perform
-->

<template>
  <Card class="mod-perform-activitySection">
    <Grid>
      <GridItem :units="8"/>
      <GridItem :units="4">
        <div class="mod-perform-activitySection__action-buttons">
          <Button
            :text="$str('edit_content', 'mod_perform')"
            @click="modelOpen = true"
          />
        </div>
      </GridItem>
    </Grid>
    <Grid :stack-at="768">
      <GridItem :units="5">
        <h3 class="mod-perform-activitySection__participant-heading">
          Answering Participants
        </h3>
        <ButtonIcon :aria-label="'Add answering participants'">
          <AddIcon size="200" />
        </ButtonIcon>
      </GridItem>

      <GridItem :units="1" />

      <GridItem :units="5">
        <h3 class="mod-perform-activitySection__participant-heading">
          Viewing participants
        </h3>
        <ButtonIcon :aria-label="'Add viewing participants'">
          <AddIcon size="200" />
        </ButtonIcon>
      </GridItem>
    </Grid>

    <ModalPresenter :open="modelOpen" @request-close="modalRequestClose">
      <EditSectionContentModal />
    </ModalPresenter>
  </Card>
</template>

<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import Button from 'totara_core/components/buttons/Button';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Card from 'totara_core/components/card/Card';
import EditSectionContentModal from 'mod_perform/components/manage_activity/EditSectionContentModal';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';

export default {
  components: {
    AddIcon,
    Button,
    ButtonIcon,
    Card,
    EditSectionContentModal,
    Grid,
    GridItem,
    ModalPresenter,
  },
  props: {
    value: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      modelOpen: false,
    };
  },
  methods: {
    updateSection(update) {
      const newValue = Object.assign({}, this.value, update);

      this.$emit('input', newValue);
    },
    modalRequestClose() {
      this.modelOpen = false;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "edit_content"
    ]
  }
</lang-strings>
