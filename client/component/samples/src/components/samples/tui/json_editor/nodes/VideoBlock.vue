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
  @module totara_samples
-->

<template>
  <CoreVideoBlock :url="url" mimetype="video/mp4" filename="Some file" />
</template>

<script>
import CoreVideoBlock from 'tui/components/json_editor/nodes/VideoBlock';

export default {
  components: {
    CoreVideoBlock,
  },

  data() {
    return {
      url:
        'http://totara.local/draftfile.php/5/user/draft/122430750/2020-01-13%2016-09-12.mp4',
    };
  },
};
</script>
