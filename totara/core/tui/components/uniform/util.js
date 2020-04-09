/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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

import { pick } from 'totara_core/util';
import { getPropDefs } from 'totara_core/vue_util';
import FormField from 'totara_core/components/uniform/FormField';

/**
 * Create a wrapper conponent for an input.
 *
 * Only requirement is that input takes a value prop and emits an "input" event
 * with the new value.
 *
 * @param {(object|Vue)} input Input component to wrap.
 * @param {object} [options]
 * @param {boolean} [options.passAriaLabelledby] Pass the label ID as aria-labelledby.
 * @returns {(object|Vue)} Vue component.
 */
export function createUniformInputWrapper(input, options = {}) {
  const inputProps = getPropDefs(input);
  delete inputProps.name;
  delete inputProps.validate;
  delete inputProps.validations;
  delete inputProps.id;
  delete inputProps.ariaDescribedby;
  delete inputProps.ariaInvalid;
  delete inputProps.ariaLabelledby;

  const props = Object.assign({}, inputProps, {
    name: {
      type: [String, Number, Array],
      required: true,
    },

    validate: Function,
    validations: [Function, Array],
  });

  return {
    functional: true,

    // needed for lang string loader support
    components: {
      FormField,
      Input: input,
    },

    inheritAttrs: false,

    props: props,

    render(h, { props, children }) {
      const propsForInput = pick(props, Object.keys(inputProps));
      return h(FormField, {
        props: {
          name: props.name,
          validate: props.validate,
          validations: props.validations,
        },
        scopedSlots: {
          default: ({
            id,
            value,
            update,
            blur,
            inputName,
            errorId,
            labelId,
          }) => {
            const finalProps = Object.assign({}, propsForInput, {
              value,
            });
            // attrs that are actually props will automatically be moved to
            // props by Vue
            const attrs = {
              id,
              name: inputName,
              'aria-describedby': errorId || null,
              'aria-invalid': errorId ? 'true' : null,
            };
            if (options.passAriaLabelledby) {
              attrs['aria-labelledby'] = labelId;
            }
            return h(
              input,
              {
                props: finalProps,
                attrs,
                on: {
                  input: value => update(value),
                  blur,
                },
              },
              children
            );
          },
        },
      });
    },
  };
}
