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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module performelement_long_text
-->

<template>
  <div class="tui-longTextAdminView">
    <Form input-width="full" :vertical="true">
      <FormRow>
        <Textarea :rows="6" name="answer_text" :char-length="50" />
      </FormRow>
    </Form>
  </div>
</template>

<script>
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import Textarea from 'tui/components/form/Textarea';

export default {
  components: {
    Form,
    FormRow,
    Textarea,
  },

  inheritAttrs: false,
};
</script>
