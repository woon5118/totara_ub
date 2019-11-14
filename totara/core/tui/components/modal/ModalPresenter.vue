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
import PropsProvider from 'totara_core/components/util/PropsProvider';

export const presenterInterfaceName = 'modal-presenter-interface';

/**
 * Shows and hides the modal, recreating the widget each time
 */
export default {
  components: {
    PropsProvider,
  },

  props: {
    open: Boolean,
  },

  provide() {
    return {
      [presenterInterfaceName]: {
        setIsOpen: open => {
          this.isOpen = open;
        },
        requestClose: this.$_requestClose,
        data: this.childData,
      },
    };
  },

  data() {
    return {
      isOpen: false,
      childData: { open: false },
    };
  },

  watch: {
    open(open) {
      this.childData.open = open;
    },
  },

  methods: {
    $_provide() {
      return {
        listeners: { 'request-close': this.$_requestClose },
      };
    },

    $_requestClose(e) {
      this.$emit('request-close', e || {});
    },
  },

  render(h) {
    // open: should we be open
    // isOpen: are we currently open (is true until modal *finishes* closing)
    if (this.open || this.isOpen) {
      return h(
        'PropsProvider',
        { props: { provide: this.$_provide } },
        this.$scopedSlots.default()
      );
    }
    return null;
  },
};
</script>
