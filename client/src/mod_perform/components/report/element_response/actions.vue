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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_perform
-->

<template>
  <div>
    <ButtonIcon
      :aria-label="$str('preview', 'mod_perform')"
      :styleclass="{ transparentNoPadding: true }"
      @click="showPreviewModal"
    >
      <PreviewIcon />
    </ButtonIcon>

    <a
      :href="exportUrl"
      :aria-label="$str('button_export', 'mod_perform')"
      :title="$str('button_export', 'mod_perform')"
    >
      <DownloadIcon />
    </a>

    <ModalPresenter :open="openPreviewModal" @request-close="closePreviewModal">
      <QuestionElementPreviewModal
        :element-id="elementId"
        @modal-close="closePreviewModal"
      />
    </ModalPresenter>
  </div>
</template>
<script>
import QuestionElementPreviewModal from 'mod_perform/components/report/element_response/QuestionElementPreviewModal';
import DownloadIcon from 'tui/components/icons/common/Download';
import PreviewIcon from 'tui/components/icons/common/Preview';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import ModalPresenter from 'tui/components/modal/ModalPresenter';

export default {
  components: {
    ButtonIcon,
    DownloadIcon,
    PreviewIcon,
    QuestionElementPreviewModal,
    ModalPresenter,
  },
  props: {
    elementId: {
      required: true,
      type: Number,
    },
  },
  data() {
    return {
      openPreviewModal: false,
    };
  },
  computed: {
    exportUrl() {
      return this.$url('/mod/perform/reporting/performance/export.php', {
        element_id: this.elementId,
      });
    },
  },
  methods: {
    closePreviewModal() {
      this.openPreviewModal = false;
    },
    showPreviewModal() {
      this.openPreviewModal = true;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "activity_participants_add",
      "subject_instance_availability_reopen",
      "button_export",
      "preview"
    ]
  }
</lang-strings>
