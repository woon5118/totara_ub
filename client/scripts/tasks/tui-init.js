/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
 */

const fs = require('fs');
const path = require('path');
const { rootDir } = require('../lib/common');
const { formatCodeWithPath } = require('../lib/prettier');
const { getClientDir } = require('../lib/resolution');

const args = require('yargs')
  .help()
  .version(false)
  .command('$0 <component>', false, yargs => {
    yargs
      .positional('component', {
        describe: 'Totara component to initialise TUI in, e.g. mod_foo',
      })
      .describe(
        'vendor',
        'unique string identifying the authoring organisation'
      )
      .default('vendor', 'totara');
  }).argv;

const { clientdir } = getClientDir(args.component);
if (clientdir === null) {
  console.error(
    `Error: unknown component ${args.component}.\n` +
      `If this is a new core component or plugin type, you may need to run\n` +
      `'php totara/core/dev/generate_tui_data.php' to generate required data.\n`
  );
  process.exit(1);
}
const fullDir = path.join(rootDir, clientdir);

if (fs.existsSync(fullDir)) {
  console.error(`Error: directory ${clientdir} already exists.`);
  process.exit(1);
}

fs.mkdirSync(fullDir);

/**
 * Write a file if it does not exist, formatting content with prettier.
 *
 * @param {string} file
 * @param {string} content
 */
function write(file, content) {
  const filePath = path.join(fullDir, file);
  if (fs.existsSync(filePath)) {
    console.log(`${file} already exists, skipping...`);
    return;
  }
  if (file.endsWith('.json') && typeof content !== 'string') {
    content = JSON.stringify(content, null, 2);
  }
  content = formatCodeWithPath(filePath, content);
  fs.writeFileSync(filePath, content, 'utf8');
}

console.log(`Initializing TUI in ${getClientDir}/...`);

const coreTuiRelative = path.relative(
  clientdir,
  getClientDir('tui')
);

/**
 * Generate a string that could be passed to require for a file in core TUI dir.
 *
 * @param {string} file
 * @returns {string}
 */
function coreTuiRequire(file) {
  let requirePath = path.join(coreTuiRelative, file);
  if (!path.isAbsolute(requirePath) && requirePath[0] != '.') {
    requirePath = './' + requirePath;
  }
  return requirePath;
}

write('tui.json', { component: args.component, vendor: args.vendor });
write(
  '.eslintrc.js',
  `module.exports = {
  extends: [${JSON.stringify(
    coreTuiRequire('scripts/configs/.eslintrc_tui.js')
  )}]
};`
);
write(
  '.stylelintrc.js',
  `module.exports = {
  extends: [${JSON.stringify(
    coreTuiRequire('scripts/configs/.stylelintrc_tui.js')
  )}]
};`
);

['js', 'pages', 'components', 'tests', 'styles', 'tests/unit'].forEach(
  subdir => {
    const fullSubdir = path.join(fullDir, subdir);
    if (!fs.existsSync(fullSubdir)) {
      fs.mkdirSync(fullSubdir);
    }
  }
);

console.log('Done!');
