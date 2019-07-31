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
  @module samples
-->

<template>
  <div>
    <div>
      <p>With color</p>
      <AccessIcon :access="access.public" />
      <AccessIcon :access="access.private" />
      <AccessIcon :access="access.restricted" />
    </div>

    <div>
      <p>Without color</p>
      <AccessIcon :coloring="false" :access="access.public" />
      <AccessIcon :coloring="false" :access="access.private" />
      <AccessIcon :coloring="false" :access="access.restricted" />
    </div>
  </div>
</template>

<script>
import AccessIcon from 'totara_engage/components/icons/access/computed/AccessIcon';
import { AccessManager } from 'totara_engage/index';

export default {
  components: {
    AccessIcon,
  },

  data() {
    return {
      access: {
        public: AccessManager.PUBLIC,
        private: AccessManager.PRIVATE,
        restricted: AccessManager.RESTRICTED,
      },
    };
  },
};
</script>
