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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module totara_perform
-->

<template>
  <div>
    <a
      :href="exportUrl"
      :aria-label="$str('button_export', 'mod_perform')"
      :title="$str('button_export', 'mod_perform')"
    >
      <DownloadIcon />
    </a>
  </div>
</template>
<script>
import DownloadIcon from 'tui/components/icons/common/Download';

export default {
  components: {
    DownloadIcon,
  },
  props: {
    subjectInstanceId: {
      required: true,
      type: Number,
    },
  },
  computed: {
    exportUrl() {
      return this.$url('/mod/perform/reporting/performance/export.php', {
        action: 'item',
        export: 'Export',
        format: 'csv',
        subject_instance_id: this.subjectInstanceId,
      });
    },
  },
};
</script>
<lang-strings>
{
  "mod_perform": [
    "button_export"
  ]
}
</lang-strings>
