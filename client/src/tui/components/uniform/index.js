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
export { default as FormColor } from 'totara_core/components/uniform/FormColor';
export { default as FormToggleButton } from 'totara_core/components/uniform/FormToggleButton';
export { default as FormTel } from 'totara_core/components/uniform/FormTel';
export { default as FormText } from 'totara_core/components/uniform/FormText';
export { default as FormTextarea } from 'totara_core/components/uniform/FormTextarea';
export { default as FormUrl } from 'totara_core/components/uniform/FormUrl';
export { default as FormRange } from 'totara_core/components/uniform/FormRange';

// util
export { createUniformInputWrapper } from './util';
