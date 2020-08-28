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

  @author Brian Barnes <brian.barnes@totaralearning.com>
  @module totara_engage
-->

<template>
  <Modal
    size="small"
    aria-labelledby="tui-engage__components__modal__namelistmodal-title"
  >
    <h2
      id="tui-engage__components__modal__namelistmodal-title"
      class="tui-engage__components__modal__namelistmodal-title"
    >
      {{ title }}
    </h2>
    <CloseButton
      class="tui-engage__components__modal__namelistmodal-close"
      :size="300"
      @click="$emit('dismiss')"
    />
    <div
      ref="watchTarget"
      class="tui-engage__components__modal__namelistmodal-content"
    >
      <Loading v-if="loading" />
      <ul
        v-else
        class="tui-engage__components__modal__namelistmodal-content-list"
      >
        <li v-for="profile in profiles" :key="profile.id">
          <Avatar :src="profile.src" alt="" size="xsmall" />
          <a :href="profileUrl(profile.id)">
            {{ profile.name }}
          </a>
        </li>
      </ul>
      <div
        v-if="!allLoaded"
        ref="loadMore"
        class="tui-engage__components__modal__namelistmodal-content-loadmore"
      >
        <Loading />
        {{ $str('loading', 'core') }}
      </div>
    </div>
  </Modal>
</template>

<script>
import Modal from 'tui/components/modal/Modal';
import Avatar from 'tui/components/avatar/Avatar';
import CloseButton from 'tui/components/buttons/CloseIcon';
import Loading from 'tui/components/icons/Loading';

export default {
  components: {
    Avatar,
    Modal,
    CloseButton,
    Loading,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    allLoaded: {
      type: Boolean,
      default: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    profiles: {
      default: () => [
        {
          name: '',
          src: '',
        },
      ],
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      watching: false,
    };
  },

  watch: {
    allLoaded() {
      this.detectLoad();
    },
  },

  mounted() {
    this.detectLoad();
  },

  methods: {
    triggerLoad() {
      if (!this.$refs.loadMore) {
        return;
      }

      const scrollHeight =
        this.$refs.watchTarget.scrollHeight -
        this.$refs.watchTarget.clientHeight -
        this.$refs.loadMore.clientHeight * 2;

      if (this.$refs.watchTarget.scrollTop > scrollHeight) {
        this.$emit('load-more');
        this.$refs.watchTarget.removeEventListener('scroll', this.triggerLoad);

        // Delay re-applying the listener
        setTimeout(() => {
          if (!this.allLoaded) {
            this.$refs.watchTarget.addEventListener('scroll', this.triggerLoad);
          }
        }, 1000);
      }
    },

    detectLoad() {
      if (this.$refs.loadMore) {
        this.$refs.watchTarget.addEventListener('scroll', this.triggerLoad);
      } else {
        this.watching = false;
        this.$refs.watchTarget.removeEventListener('scroll', this.triggerLoad);
      }
    },

    profileUrl(id) {
      return this.$url('/user/profile.php', { id });
    },
  },
};
</script>

<lang-strings>
  {
    "core": [
      "loading"
    ]
  }
</lang-strings>
