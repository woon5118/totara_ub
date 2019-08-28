/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

const stylelint = require('stylelint');

const ruleName = 'tui/ascii-only';
const messages = stylelint.utils.ruleMessages(ruleName, {
  rejected: 'Non-ascii character',
});

module.exports = stylelint.createPlugin(ruleName, on => {
  return (root, result) => {
    if (!on) {
      return;
    }

    const reportFromIndex = index => {
      stylelint.utils.report({
        message: messages.rejected,
        node: root,
        index,
        result,
        ruleName,
      });
    };

    const css = root.source.input.css;
    // eslint-disable-next-line no-control-regex
    const match = /[^\x00-\x7F]/.exec(css);
    if (match) {
      reportFromIndex(match.index);
    }
  };
});

module.exports.ruleName = ruleName;
module.exports.messages = messages;
