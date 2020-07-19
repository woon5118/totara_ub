<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module samples
-->

<template>
  <div class="tui-samples">
    <Layout>
      <template v-slot:page-title>
        {{ $str('pluginname', 'totara_tui') }}
      </template>
      <template v-slot:left>
        <FilterSidePanel title="Filter results">
          <SearchFilter
            v-model="filter"
            label="Filter components"
            :show-label="true"
            :stacked="true"
          />

          <SelectFilter
            v-model="totaraComponentSelection"
            label="Within plugin"
            :show-label="true"
            :stacked="true"
            :options="totaraComponentOptions"
          />

          <div class="tui-samples__filter">
            <div class="tui-samples__results">
              <div v-for="(group, i) in resultGroups" :key="i">
                <div class="tui-samples__resultGroupHeader">
                  {{ group.name }}
                </div>
                <a
                  v-for="result in group.results"
                  :key="result.component"
                  class="tui-samples__result"
                  :class="{
                    'tui-samples__result--selected': sample == result,
                  }"
                  :href="sampleUrl(result)"
                  @click.prevent="select(result)"
                >
                  {{ result.text }}
                </a>
              </div>
            </div>
          </div>
        </FilterSidePanel>
      </template>
      <template v-slot:right>
        <component :is="component" />
      </template>
    </Layout>
  </div>
</template>

<script>
import { memoize, unique } from 'tui/util';
import { formatParams } from 'tui/url';
import Card from 'tui/components/card/Card';
import FilterSidePanel from 'tui/components/filters/FilterSidePanel';
import Layout from 'tui/components/layouts/LayoutTwoColumn';
import MultiSelect from 'tui/components/filters/MultiSelectFilter';
import SearchFilter from 'tui/components/filters/SearchFilter';
import SelectFilter from 'tui/components/filters/SelectFilter';

const prefix = 'samples/components/samples/';

const underscores = str => {
  str = str.replace(/(.)([A-Z][a-z]+)/g, '$1_$2');
  str = str.replace(/([a-z0-9])([A-Z])/g, '$1_$2');
  return str.toLowerCase();
};

const wrapSampleComponent = memoize(sample => {
  return () => ({
    component: tui
      // eslint-disable-next-line tui/no-tui-internal
      ._loadTotaraComponent(sample.totaraComponent)
      .then(() => tui.loadComponent(sample.component)),
    error: tui.defaultExport(
      tui.require('tui/components/errors/ErrorPageRender')
    ),
  });
});

export default {
  components: {
    Card,
    FilterSidePanel,
    MultiSelect,
    SearchFilter,
    SelectFilter,
    Layout,
  },

  data() {
    return {
      filter: '',
      samples: [],
      sample: null,
      totaraComponent: null,
    };
  },

  computed: {
    component() {
      if (this.sample) {
        return wrapSampleComponent(this.sample);
      }
      return null;
    },

    results() {
      const filter = this.filter.toLowerCase();
      return this.samples
        .filter(
          x =>
            !this.totaraComponent || x.totaraComponent == this.totaraComponent
        )
        .map(x => ({
          sample: x,
          score: this.$_score(x.text, filter),
        }))
        .filter(x => x.score != 0)
        .sort((a, b) => b.score - a.score)
        .map(x => x.sample);
    },

    resultGroups() {
      const results = this.results;
      const groups = [];
      let lastGroup = null;
      results.forEach(result => {
        if (lastGroup && lastGroup.name == result.totaraComponent) {
          lastGroup.results.push(result);
        } else {
          lastGroup = { name: result.totaraComponent, results: [result] };
          groups.push(lastGroup);
        }
      });
      return groups;
    },

    totaraComponentOptions() {
      return [{ id: null, label: 'All' }].concat(
        unique(this.samples.map(x => x.totaraComponent))
      );
    },

    totaraComponentSelection: {
      get() {
        return this.totaraComponent;
      },

      set(value) {
        this.totaraComponent = value;
        this.$_pushState();
      },
    },
  },

  mounted() {
    this.samples = tui
      // eslint-disable-next-line tui/no-tui-internal
      ._getLoadedComponentModules('samples')
      .filter(x => x.startsWith(prefix))
      .map(x => {
        const i = x.indexOf('/', prefix.length);
        const totaraComponent = x.slice(prefix.length, i);
        return {
          component: x,
          totaraComponent,
          text: x.slice(i + 1),
          key: x.slice(prefix.length),
        };
      });

    window.addEventListener('popstate', this.$_readState);
    this.$_readState();
  },

  destroyed() {
    window.removeEventListener('popstate', this.$_readState);
  },

  methods: {
    /**
     * Update the selected result
     *
     * @param {object} result
     */
    select(result) {
      this.sample = result;
      this.$_pushState();
    },

    sampleUrl(result) {
      let params = {};
      if (this.totaraComponent) {
        params.tc = this.totaraComponent;
      }
      if (result) {
        params.component = result.key;
      }
      params = formatParams(params);
      return window.location.pathname + (params && '?' + params);
    },

    $_readState() {
      const params = window.location.search
        .replace(/^\?/, '')
        .split('&')
        .reduce((acc, part) => {
          const [key, value] = part.split('=');
          if (key) {
            acc[key] = decodeURIComponent(value);
          }
          return acc;
        }, {});

      this.totaraComponent = params.tc || null;
      const key = params.component;
      this.sample = this.samples.find(x => x.key == key);
    },

    /**
     * Push updated URL state
     */
    $_pushState() {
      history.pushState(null, null, this.sampleUrl(this.sample));
    },

    /**
     * Generate a score for ranking a filter result
     *
     * @param {string} text Result text
     * @param {string} filter Filter text
     * @returns {Number} Score, higher is better
     */
    $_score(text, filter) {
      const lowerText = text.toLowerCase();
      const index = lowerText.indexOf(filter);

      if (index === -1) {
        return 0;
      }

      let substringScore = (text.length - index) / text.length;

      let subwordScore = 0;
      const words = underscores(text).split('_');
      for (var i = 0; i < words.length; i++) {
        if (words[i].startsWith(filter)) {
          subwordScore = (words.length - i) / words.length;
          break;
        }
      }

      return subwordScore * 10 + substringScore;
    },
  },
};
</script>

<lang-strings>
{
  "totara_tui": [
    "pluginname"
  ]
}
</lang-strings>

<style lang="scss">
.tui-samples {
  .formHeader {
    margin-top: var(--tui-gap-6);
  }

  &__filter {
    display: flex;
    flex-direction: column;
    flex-grow: 1;

    > * + * {
      margin-top: 1.6rem;
    }
  }

  &__results {
    display: flex;
    flex-direction: column;
    max-height: 50vh;
    overflow-x: hidden;
    overflow-y: auto;
  }

  &__result {
    display: block;
    padding: var(--tui-gap-1) var(--tui-gap-2);
    color: var(--tui-color-state);

    &:hover,
    &:focus {
      color: var(--tui-color-state-focus);
      text-decoration: none;
      background-color: var(--tui-color-state-highlight);
    }

    &--selected,
    &--selected:hover,
    &--selected:focus {
      color: var(--tui-color-state);
      background-color: var(--tui-color-neutral-3);
      border-radius: 3px;
    }
  }

  &__resultGroupHeader {
    margin-top: var(--tui-gap-1);
    color: var(--tui-color-neutral-6);
    font-weight: bold;
    font-size: var(--tui-font-size-11);
    text-transform: uppercase;
  }

  &--highlight {
    color: pink;
    background: pink;
  }
}
</style>
