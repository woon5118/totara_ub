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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module totara_reaction
-->
<template>
  <Modal class="tui-likeRecordsModal">
    <ModalContent :close-button="true">
      <template v-slot:title>
        <h2 class="tui-likeRecordsModal__title">
          <span>
            {{ $str('likesx', 'totara_reaction', like.count) }}
          </span>
          <Loading v-if="$apollo.loading" />
        </h2>
      </template>

      <div class="tui-likeRecordsModal__content">
        <ul class="tui-likeRecordsModal__content__records">
          <li v-for="({ user }, index) in like.reactions" :key="index">
            <Avatar
              :src="user.profileimageurl"
              :alt="user.profileimagealt || ''"
              size="xsmall"
            />

            <a :href="$url('/user/profile.php', { id: user.id })">
              {{ user.fullname }}
            </a>
          </li>
        </ul>
      </div>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import Avatar from 'tui/components/avatar/Avatar';
import Loading from 'tui/components/icons/common/Loading';

// GraphQL
import getLikes from 'totara_reaction/graphql/get_likes';

export default {
  components: {
    Modal,
    ModalContent,
    Avatar,
    Loading,
  },

  props: {
    instanceId: {
      type: [String, Number],
      required: true,
    },

    component: {
      type: String,
      required: true,
    },

    area: {
      type: String,
      required: true,
    },
  },

  apollo: {
    like: {
      query: getLikes,
      fetchPolicy: 'network-only',
      variables() {
        return {
          component: this.component,
          area: this.area,
          instanceid: this.instanceId,
        };
      },

      update({ count, reactions }) {
        return { count, reactions };
      },
    },
  },

  data() {
    return {
      page: 1,
      like: {
        count: 0,
        reactions: [],
      },
    };
  },
};
</script>

<lang-strings>
  {
    "totara_reaction": [
      "likesx"
    ]
  }
</lang-strings>
