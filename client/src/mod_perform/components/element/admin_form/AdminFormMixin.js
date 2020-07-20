/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @module mod_perform
 */

export default {
  mounted() {
    // Forces the form to be in the view and the first input to be selected when adding or changing to edit mode.
    this.$el.scrollIntoView();
    this.$el.querySelector('input').focus();
  },
};
