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

const generatorError =
  'Generators are not allowed due to performance considerations in IE 11.';

function checkGenerator(context, node) {
  if (node.generator) {
    context.report(node, generatorError);
  }
}

module.exports = {
  rules: {
    'no-generators': {
      create: context => ({
        FunctionExpression(node) {
          checkGenerator(context, node);
        },
        FunctionDeclaration(node) {
          checkGenerator(context, node);
        },
      }),
    },

    'no-export-vue-extend': {
      create: context => ({
        ExportDefaultDeclaration(node) {
          if (node.declaration.type != 'CallExpression') {
            return;
          }
          const callee = node.declaration.callee;
          const isMatch =
            callee.type == 'MemberExpression' &&
            callee.object.type == 'Identifier' &&
            callee.object.name == 'Vue' &&
            callee.property.type == 'Identifier' &&
            callee.property.name == 'extend';
          if (isMatch) {
            context.report(
              callee,
              'Avoid using Vue.extend to define exported components as it ' +
                'will not work with theme inheritance.'
            );
          }
        },
      }),
    },

    'no-object-spread': {
      create: context => ({
        SpreadElement(node) {
          if (node.parent.type == 'ObjectExpression') {
            context.report(node, 'Object spread is not supported by Edge.');
          }
        },
      }),
    },

    'no-for-of': {
      create: context => ({
        ForOfStatement(node) {
          context.report(
            node,
            'for..of is not supported by IE 11 and cannot be polyfilled ' +
              'perfomantly. Try using .forEach or a plain for loop instead.'
          );
        },
      }),
    },

    'no-tui-internal': {
      create: context => ({
        MemberExpression(node) {
          if (
            node.object.type == 'Identifier' &&
            node.object.name == 'tui' &&
            node.property.type == 'Identifier' &&
            node.property.name[0] == '_'
          ) {
            context.report(
              node.property,
              'Do not access internal TUI properties, as they may change without warning.'
            );
          }
        },
      }),
    },
  },
};
