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
  @module totara_engage
-->

<template>
  <div class="tui-accessSetting">
    <ModalPresenter :open="showModal" @request-close="showModal = false">
      <AccessModal
        :item-id="itemId"
        :component="component"
        :selected-access="accessValue"
        :selected-options="selectedOptions"
        :restricted-disabled="restrictedDisabled"
        :private-disabled="privateDisabled"
        :selected-time-view="selectedTimeView"
        :enable-time-view="enableTimeView"
        :submitting="submitting"
        @done="submit"
      />
    </ModalPresenter>

    <AccessDisplay
      slot="content"
      :access-value="accessValue"
      :topics="topics"
      :time-view="selectedTimeView"
      @request-open="showModal = true"
    />
  </div>
</template>

<script>
import AccessModal from 'totara_engage/components/modal/AccessModal';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import { AccessManager } from 'totara_engage/index';
import AccessDisplay from 'totara_engage/components/sidepanel/access/AccessDisplay';

export default {
  components: {
    AccessModal,
    ModalPresenter,
    AccessDisplay,
  },

  props: {
    submitting: {
      type: Boolean,
      default: false,
    },

    itemId: {
      type: [Number, String],
      required: true,
    },

    component: {
      type: String,
      required: true,
    },

    accessValue: {
      type: String,
      required: true,
      validator(prop) {
        return AccessManager.isValid(prop);
      },
    },

    topics: {
      type: Array,
      default() {
        return [];
      },
    },

    shares: {
      type: Array,
      default() {
        return [];
      },
    },

    openModal: Boolean,

    selectedTimeView: {
      type: String,
      default: null,
    },

    enableTimeView: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      showModal: this.openModal,
    };
  },

  computed: {
    /**
     * @return {Object}
     */
    selectedOptions() {
      return {
        shares: this.shares,
        topics: this.topics,
      };
    },

    /**
     *
     * @returns {boolean}
     */
    restrictedDisabled() {
      return AccessManager.isPublic(this.accessValue);
    },

    privateDisabled() {
      return (
        AccessManager.isPublic(this.accessValue) ||
        AccessManager.isRestricted(this.accessValue)
      );
    },
  },

  watch: {
    showModal(value) {
      if (!value) {
        this.$emit('close-modal');
      }
    },
    openModal(value) {
      this.showModal = value;
    },
  },

  methods: {
    /**
     *
     * @param {String} access
     * @param {Array} topics
     * @param {Array} shares
     * @param {String} timeView
     */
    submit({ access, topics, shares, timeView }) {
      this.showModal = false;
      this.$emit('access-update', { access, topics, shares, timeView });
    },
  },
};
</script>
