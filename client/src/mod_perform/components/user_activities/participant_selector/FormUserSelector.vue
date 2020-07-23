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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<!--
  Required in order to use the UserSelector within Uniform.
-->

<script>
import { createUniformInputWrapper } from 'totara_core/components/uniform/util';
import UserSelector from 'mod_perform/components/user_activities/participant_selector/UserSelector';

export default createUniformInputWrapper(UserSelector);
</script>
