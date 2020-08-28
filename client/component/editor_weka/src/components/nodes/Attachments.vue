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
  @module editor_weka
-->

<template>
  <AttachmentNodeCollection class="tui-editorWeka-attachments">
    <div ref="content" class="tui-editorWeka-attachments__content">
      <!-- Attachments collection. A content placeholder that will hold the list of the attachments in here. -->
    </div>
  </AttachmentNodeCollection>
</template>

<script>
import AttachmentNodeCollection from 'tui/components/json_editor/nodes/AttachmentNodeCollection';

export default {
  components: {
    AttachmentNodeCollection,
  },
};
</script>
