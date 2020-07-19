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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module totara_perform
-->

<template>
  <div>
    <OpenCloseActionModal
      :modal-open="showModalOpen"
      :subject-instance-id="subjectInstanceId"
      :is-open="isOpen"
      @modal-close="modalClose"
    />

    <a
      :href="participationManagementUrl"
      :title="$str('activity_participants_add', 'mod_perform')"
    >
      <ParticipantAddIcon />
    </a>
    <ButtonIcon
      v-if="isOpen"
      :aria-label="$str('button_close', 'mod_perform')"
      :styleclass="{ transparentNoPadding: true }"
      @click="showModal()"
    >
      <LockIcon />
    </ButtonIcon>
    <ButtonIcon
      v-else
      :aria-label="$str('subject_instance_availability_reopen', 'mod_perform')"
      :styleclass="{ transparentNoPadding: true }"
      @click="showModal()"
    >
      <UnlockIcon />
    </ButtonIcon>
  </div>
</template>
<script>
import ParticipantAddIcon from 'tui/components/icons/common/AddUser';
import OpenCloseActionModal from 'mod_perform/components/report/subject_instance/OpenCloseActionModal';
import LockIcon from 'tui/components/icons/common/Lock';
import UnlockIcon from 'tui/components/icons/common/Unlock';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
export default {
  components: {
    ButtonIcon,
    ParticipantAddIcon,
    LockIcon,
    UnlockIcon,
    OpenCloseActionModal,
  },
  props: {
    activityId: {
      type: String,
    },
    subjectInstanceId: {
      type: String,
    },
    isOpen: {
      type: Boolean,
    },
  },
  data() {
    return {
      showModalOpen: false,
    };
  },
  computed: {
    /**
     * Get the url to the participation management
     *
     * @return {string}
     */
    participationManagementUrl() {
      return this.$url(
        '/mod/perform/manage/participation/add_participants.php',
        {
          subject_instance_id: this.subjectInstanceId,
        }
      );
    },
  },
  methods: {
    modalClose() {
      this.showModalOpen = false;
    },
    showModal() {
      this.showModalOpen = true;
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform": [
    "activity_participants_add",
    "subject_instance_availability_reopen",
    "button_close"
  ]
  }
</lang-strings>
