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
import ErrorPageRender from 'totara_core/presentation/errors/ErrorPageRender';

export default {
  // must declare here so it is picked up by lang string scanner
  components: {
    ErrorPageRender,
  },

  data() {
    return {
      errored: false,
      error: null,
    };
  },

  errorCaptured(err, vm, info) {
    // we only care about render errors - we don't want to unmount the
    // entire tree because an event handler threw an exception
    if (info == 'render') {
      // report error normally
      setTimeout(() => {
        throw err;
      });
      this.errored = true;
      this.error = err;
      // mark this error as handled
      return false;
    }
  },

  methods: {
    /**
     * Try to render again.
     *
     * All the original components will have been unmounted so all component
     * state will be reset.
     */
    retry() {
      this.errored = false;
    },
  },

  render(h) {
    if (this.errored) {
      return h(ErrorPageRender, {
        props: { error: this.error, retryable: true },
        on: { retry: this.retry },
      });
    } else {
      return this.$scopedSlots.default();
    }
  },
};
</script>
