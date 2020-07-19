<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<script>
export default {
  // only functional components are able to render with multiple root elements
  functional: true,

  props: {
    data: {
      type: Object,
      required: true,
    },
    title: {
      type: String,
      default: undefined,
    },
    alt: {
      type: String,
      default: undefined,
    },
    size: {
      type: [Number, String],
      default: undefined,
    },
    customClass: {
      type: [String, Object, Array],
      default: undefined,
    },
  },

  render(h, { props }) {
    return [
      h(
        'span',
        {
          class: [
            'flex-icon ft-fw ft-stack',
            props.size && 'ft-size-' + props.size,
            props.customClass,
          ],
          attrs: {
            'aria-hidden': 'true',
            'data-flex-icon': props.data.identifier,
            title: props.title,
          },
        },
        props.data.classes.map(x => h('span', { class: ['ft ft-fw', x] }))
      ),
      props.alt && h('span', { class: 'sr-only' }, props.alt),
    ];
  },
};
</script>
