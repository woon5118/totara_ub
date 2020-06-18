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

// form
export { default as Uniform } from 'totara_core/components/uniform/Uniform';
export { default as FormField } from 'totara_core/components/uniform/FormField';
export { default as FieldArray } from 'totara_core/components/reform/FieldArray';
export { default as FormScope } from 'totara_core/components/reform/FormScope';
export { default as FormRow } from 'totara_core/components/form/FormRow';
export { default as FormRowFieldset } from 'totara_core/components/form/FormRowFieldset';

// inputs
export { default as FormCheckbox } from 'totara_core/components/uniform/FormCheckbox';
export { default as FormCheckboxGroup } from 'totara_core/components/uniform/FormCheckboxGroup';
export { default as FormDateSelector } from 'totara_core/components/uniform/FormDateSelector';
export { default as FormEmail } from 'totara_core/components/uniform/FormEmail';
export { default as FormNumber } from 'totara_core/components/uniform/FormNumber';
export { default as FormPassword } from 'totara_core/components/uniform/FormPassword';
export { default as FormRadioGroup } from 'totara_core/components/uniform/FormRadioGroup';
export { default as FormSearch } from 'totara_core/components/uniform/FormSearch';
export { default as FormSelect } from 'totara_core/components/uniform/FormSelect';
export { default as FormTel } from 'totara_core/components/uniform/FormTel';
export { default as FormText } from 'totara_core/components/uniform/FormText';
export { default as FormTextarea } from 'totara_core/components/uniform/FormTextarea';
export { default as FormUrl } from 'totara_core/components/uniform/FormUrl';

// util
export { createUniformInputWrapper } from './util';
