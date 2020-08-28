<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_engage
-->

<template>
  <div ref="sharedBoard" class="tui-sharedBoard">
    <div ref="sharedBoardInfo">
      <p class="tui-sharedBoard__label">
        {{ sharedWithLabel }}
      </p>
      <Button
        v-if="computedReceipts.length"
        :aria-expanded="showReceipts"
        :text="$str(showReceipts ? 'hide' : 'show', 'moodle')"
        :styleclass="{
          small: 'true',
          transparent: 'true',
        }"
        @click="showReceipts = !showReceipts"
      />
    </div>

    <Card v-if="showReceipts" class="tui-sharedBoard__content">
      <div class="tui-sharedBoard__content__container">
        <ul
          v-for="(item, index) in computedReceipts"
          :key="`shareBoardReceipt_${index}`"
          class="tui-sharedBoard__content__tags"
        >
          <li>
            <Tag :text="item" />
          </li>
        </ul>
      </div>
    </Card>
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import Card from 'tui/components/card/Card';
import Tag from 'tui/components/tag/Tag';

// GraphQL
import engageAdvancedFeatures from 'totara_engage/graphql/advanced_features';

export default {
  components: {
    Button,
    Card,
    Tag,
  },

  props: {
    receipts: {
      type: Object,
      default: () => ({
        people: [],
        workspaces: [],
      }),
    },
  },

  data() {
    return {
      showReceipts: false,
    };
  },

  apollo: {
    features: {
      query: engageAdvancedFeatures,
    },
  },

  computed: {
    receiptsLangstring() {
      return {
        people: this.receipts.people.length,
        workspaces: this.receipts.workspaces.length,
      };
    },

    computedReceipts() {
      return this.receipts.people.concat(this.receipts.workspaces);
    },

    featureWorkspaces() {
      return this.features && this.features.workspaces;
    },

    sharedWithLabel() {
      return this.$str(
        this.featureWorkspaces
          ? 'sharedwithpeopleworkspaces'
          : 'sharedwithpeople',
        'totara_engage',
        this.receiptsLangstring
      );
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "hide",
      "show"
    ],

    "totara_engage": [
      "sharedwithpeople",
      "sharedwithpeopleworkspaces"
    ]
  }
</lang-strings>
