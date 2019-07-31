<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module samples
-->

<template>
  <div>
    <MiniProfileCard
      :no-border="true"
      :interactive="false"
      :avatar-src="card.url"
      :avatar-alt="card.alt"
      :name="card.name"
      :mention="card.mention"
      :job-assignment="card.jobAssignment"
      :email="card.email"
    />
    <Separator />
    <MiniProfileCard
      :avatar-src="card.url"
      :avatar-alt="card.alt"
      :name="card.name"
      :mention="card.mention"
      :job-assignment="card.jobAssignment"
      :email="card.email"
    />
    <Separator />
    <Dropdown class="tui-totaraEngage-testMiniprofilecard">
      <template v-slot:trigger="{ toggle, isOpen }">
        <ButtonIcon
          :aria-expanded="isOpen ? 'true' : 'false'"
          :aria-label="'trigger'"
          :disabled="false"
          @click.prevent="toggle"
        >
          <AddIcon />
        </ButtonIcon>
      </template>
      <template v-for="index in 10">
        <DropdownItem
          :key="index"
          :paddingless="true"
          @click="handleClick(index)"
        >
          <MiniProfileCard
            :no-border="true"
            :interactive="false"
            :avatar-src="card.url"
            :avatar-alt="card.alt"
            :name="card.name"
            :mention="card.mention"
            :job-assignment="card.jobAssignment"
            :email="card.email"
          />
        </DropdownItem>
      </template>
    </Dropdown>
  </div>
</template>

<script>
import Separator from 'tui/components/decor/Separator';
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import AddIcon from 'tui/components/icons/common/Add';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';

export default {
  components: {
    MiniProfileCard,
    Separator,
    AddIcon,
    ButtonIcon,
    Dropdown,
    DropdownItem,
  },
  data() {
    return {
      card: {
        url: 'https://i.imgur.com/SnSRJUH.jpg',
        alt: 'alt for the avatar',
        name: 'John Smith Sinatra',
        mention: '@john-s-s',
        jobAssignment: 'Senior Project Coordinator',
        email: 'john.s@email.com',
      },
    };
  },
  methods: {
    handleClick(index) {
      console.log(index);
    },
  },
};
</script>

<style lang="scss">
.tui-totaraEngage-testMiniprofilecard {
  width: 326px;
}
</style>
