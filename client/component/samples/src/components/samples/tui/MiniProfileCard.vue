<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module samples
-->
<template>
  <div class="tui-samples-miniProfileCard">
    <MiniProfileCard
      v-for="(display, index) in users"
      :key="index"
      :display="display"
      class="tui-samples-miniProfileCard__card"
    />

    <MiniProfileCard
      :display="special"
      class="tui-samples-miniProfileCard__card"
    >
      <template v-slot:drop-down-items>
        <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
      </template>
    </MiniProfileCard>

    <MiniProfileCard
      :display="users[1]"
      class="tui-samples-miniProfileCard__card"
    >
      <template v-slot:drop-down-items>
        <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
      </template>
    </MiniProfileCard>

    <MiniProfileCard
      :display="users[0]"
      :has-shadow="true"
      class="tui-samples-miniProfileCard__card"
    >
      <template v-slot:drop-down-items>
        <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
      </template>
    </MiniProfileCard>

    <div class="tui-samples-miniProfileCard__smallerBox">
      <Separator :thick="true" :spread="true">
        CARD WITHOUT BORDER BEGIN
      </Separator>
      <MiniProfileCard
        :display="special"
        :no-border="true"
        :no-padding="true"
        class="tui-samples-miniProfileCard__card"
      >
        <template v-slot:drop-down-items>
          <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
        </template>
      </MiniProfileCard>
      <Separator :thick="true" :spread="true">
        CARD WITHOUT BORDER END
      </Separator>

      <MiniProfileCard
        :display="special"
        class="tui-samples-miniProfileCard__card"
      >
        <template v-slot:drop-down-items>
          <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
        </template>
      </MiniProfileCard>

      <Separator :thick="true" :spread="true">
        CARD WITHOUT BORDER BEGIN
      </Separator>
      <MiniProfileCard
        :display="users[1]"
        :no-border="true"
        :no-padding="true"
        class="tui-samples-miniProfileCard__card"
      >
        <template v-slot:drop-down-items>
          <DropdownItem @click="modalOpen = true">Open Modal</DropdownItem>
        </template>
      </MiniProfileCard>
      <Separator :thick="true" :spread="true">
        CARD WITHOUT BORDER END
      </Separator>

      <ModalPresenter :open="modalOpen" @request-close="modalOpen = false">
        <Modal>
          <ModalContent title="Title prop for the ModalContent" :close="true">
            <h5>This is modal content which could be easily customised</h5>
          </ModalContent>
        </Modal>
      </ModalPresenter>
    </div>
  </div>
</template>

<script>
import { config } from 'tui/config';
import { createSilhouetteImage } from '../../../../../tui/src/js/internal/placeholder_generator.js';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import Separator from 'tui/components/decor/Separator';

export default {
  components: {
    DropdownItem,
    MiniProfileCard,
    ModalPresenter,
    Modal,
    ModalContent,
    Separator,
  },

  data() {
    return {
      modalOpen: false,
      special: {
        profile_picture_alt: 'Charles F. Oliver picture',
        profile_picture_url: createSilhouetteImage('#3c9'),
        profile_url: this.$url('/user/profile.php'),
        display_fields: [
          {
            value: 'Charles F. Oliver',
            associate_url: this.$url('/user/profile.php'),
          },
          {
            value: '@herecomescharlie',
            associate_url: null,
          },
          {
            value: 'Senior Solutions Architect',
            associate_url: null,
          },
          {
            value: 'charles.f.oliver@example.com',
            associate_url: null,
          },
        ],
      },
      users: [
        {
          profile_picture_url: createSilhouetteImage('#000'),
          profile_picture_alt: 'Alvin Smith picture',
          profile_url: this.$url('/user/profile.php'),
          display_fields: [
            {
              value: 'Alvin Smith',
              associate_url: this.$url('/user/profile.php'),
            },
            {
              value: 'alvin.smith@totaralearning.com',
              associate_url: null,
            },
            {
              value: 'Penetration Tester',
              associate_url: null,
            },
          ],
        },
        {
          profile_picture_alt: null,
          profile_picture_url: null,
          profile_url: this.$url('/user/profile.php'),
          display_fields: [
            {
              value: 'Bob',
              associate_url: null,
            },
            {
              value: null,
              associate_url: null,
            },
            {
              value: null,
              associate_url: null,
            },
            {
              value: 'my homepage',
              associate_url: config.wwwroot,
            },
          ],
        },
      ],
    };
  },
};
</script>

<style lang="scss">
.tui-samples-miniProfileCard {
  display: flex;
  flex-direction: column;

  &__card {
    margin-bottom: var(--gap-3);
  }

  &__smallerBox {
    width: 100%;

    @media (min-width: $tui-screen-sm) {
      width: 30%;
    }
  }
}
</style>
