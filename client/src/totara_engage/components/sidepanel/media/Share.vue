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
  @module totara_engage
-->

<template>
  <div class="tui-shareSetting">
    <ButtonLabel
      :number="sharedByCountDisplay"
      :aria-label="$str('share', 'totara_engage')"
      @open="showResharerModal"
      @click="showReciptsModal"
      @popover-opened="showUsers"
    >
      <template v-slot:icon>
        <Share />
      </template>
      <template v-slot:hoverContent>
        <LazyList
          :no-items="$str('noshares', 'totara_engage')"
          :names="sharedByNames"
          :total="sharedByCountDisplay"
          :loading="!sharedByLoaded"
        />
      </template>
    </ButtonLabel>
    <ModalPresenter
      v-if="!owned"
      :open="reciptsModalOpen"
      @request-close="reciptsModalRequestClose"
    >
      <Modal size="normal" :dismissable="{ backdropClick: false }">
        <ModalContent
          :close-button="true"
          :title="$str('reshare', 'totara_engage')"
          @dismiss="reciptsModalRequestClose"
        >
          <div class="tui-shareSetting__recipient">
            <RecipientsSelector
              :access="accessValue"
              :owned="owned"
              :item-id="instanceId"
              :component="component"
              @pick-recipient="addNewShare"
              @remove-recipient="removeNewShare"
            />
            <SharedBoard :receipts="sharedTo" />
            <DoneCancelGroup
              @done="submit"
              @cancel="reciptsModalRequestClose"
            />
          </div>
        </ModalContent>
      </Modal>
    </ModalPresenter>

    <ModalPresenter
      v-if="sharedBy && sharedBy.length"
      :open="resharerModalOpen"
      @request-close="resharerModalRequestClose"
    >
      <NameListModal
        :title="$str('resharedbypeople', 'totara_engage', sharedBy.length)"
        :profiles="sharedBy"
        @dismiss="resharerModalRequestClose"
      />
    </ModalPresenter>
  </div>
</template>

<script>
import ButtonLabel from 'totara_engage/components/buttons/ButtonLabel';
import Share from 'tui/components/icons/common/Share';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import RecipientsSelector from 'totara_engage/components/form/access/RecipientsSelector';
import SharedBoard from 'totara_engage/components/form/SharedBoard';
import DoneCancelGroup from 'totara_engage/components/buttons/DoneCancelGroup';
import LazyList from 'totara_engage/components/sidepanel/media/LazyList';

import { AccessManager } from 'totara_engage/index';

import NameListModal from 'totara_engage/components/modal/NameListModal';

import sharesTotals from 'totara_engage/graphql/share_totals';
import shareWith from 'totara_engage/graphql/share';
import shareRecipients from 'totara_engage/graphql/share_recipients';
import sharedBy from 'totara_engage/graphql/share_sharers';

export default {
  components: {
    ButtonLabel,
    Share,
    ModalPresenter,
    Modal,
    ModalContent,
    RecipientsSelector,
    SharedBoard,
    DoneCancelGroup,
    NameListModal,
    LazyList,
  },

  apollo: {
    sharedBy: {
      query: sharedBy,
      skip: true,
      variables() {
        return {
          itemid: this.instanceId,
          component: this.component,
        };
      },
      update({ sharers }) {
        const users = sharers.map(recipient => {
          return {
            name: recipient.fullname,
            src: recipient.profileimageurlsmall,
            id: recipient.id,
          };
        });
        this.sharedByLoaded = true;
        return users;
      },
    },
    sharedToCount: {
      query: sharesTotals,
      variables() {
        return {
          itemid: this.instanceId,
          component: this.component,
        };
      },
      update({ shares: { totalrecipients } }) {
        return totalrecipients;
      },
    },

    sharedTo: {
      query: shareRecipients,
      skip: true,
      variables() {
        return {
          itemid: this.instanceId,
          component: this.component,
        };
      },
      update({ recipients }) {
        return recipients.reduce(
          (obj, item) => {
            if (item.user) {
              obj['people'].push(item.user.fullname);
            }
            if (item.other) {
              obj['workspaces'].push(item.other.fullname);
            }
            return obj;
          },
          { people: [], workspaces: [] }
        );
      },
    },
  },

  props: {
    owned: {
      type: Boolean,
      required: true,
    },
    accessValue: {
      type: String,
      required: true,
      validator(prop) {
        return AccessManager.isValid(prop);
      },
    },
    instanceId: {
      type: [String, Number],
      required: true,
    },
    component: {
      type: String,
      required: true,
    },
    sharedByCount: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      resharerModalOpen: false,
      reciptsModalOpen: false,
      access: this.accessValue,
      newShares: [],
      sharedTo: { people: [], workspaces: [] },
      sharedByLoaded: false,
      sharedByCountLocal: 0,
    };
  },

  computed: {
    sharedByCountDisplay() {
      return this.sharedByCountLocal
        ? this.sharedByCountLocal
        : this.sharedByCount;
    },

    sharedByNames() {
      if (this.sharedBy) {
        return this.sharedBy.map(sharedBy => {
          return sharedBy.name;
        });
      } else {
        return [];
      }
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
    sharedByCountDisplay: function() {
      if (this.sharedByLoaded) {
        this.$apollo.queries.sharedBy.refetch();
      }
    },
  },

  methods: {
    $_loadSharedBy: async function() {
      if (!this.sharedByLoaded) {
        await this.$apollo.queries.sharedBy.start();
      }
    },

    showUsers: async function(isOpen) {
      if (isOpen) {
        this.$_loadSharedBy();
      }
    },

    showReciptsModal() {
      if (this.owned) {
        this.$emit('access-modal');
      } else {
        this.$apollo.queries.sharedTo.start();
        this.reciptsModalOpen = true;
      }
    },
    reciptsModalRequestClose() {
      this.reciptsModalOpen = false;
    },

    showResharerModal() {
      this.resharerModalOpen = true;
    },
    resharerModalRequestClose() {
      this.resharerModalOpen = false;
    },
    addNewShare(instance) {
      const share = {
        instanceid: instance.instanceid,
        component: instance.component,
        area: instance.area,
      };
      this.newShares.push(share);
    },
    removeNewShare(instance) {
      this.newShares = this.newShares.filter(
        newShare => newShare.instanceid !== instance.instanceid
      );
    },

    submit: async function() {
      const data = await this.$apollo.mutate({
        refetchAll: false,
        mutation: shareWith,
        variables: {
          itemid: this.instanceId,
          component: this.component,
          recipients: this.newShares,
        },
      });

      this.sharedByCountLocal = data.data.shares.sharedbycount;
      this.$apollo.queries.sharedTo.refetch();
      this.reciptsModalRequestClose();
    },
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "reshare",
      "resharedbypeople",
      "share",
      "noshares"
    ]
  }
</lang-strings>
