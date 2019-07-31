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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module totara_engage
-->

<template>
  <Responsive
    :breakpoints="[
      { name: 's', boundaries: [0, 576] },
      { name: 'm', boundaries: [577, 1024] },
      { name: 'l', boundaries: [1025, 1672] },
    ]"
    class="tui-totaraEngage-cardsGrid"
    @responsive-resize="$_handleResize"
  >
    <CoreGrid
      v-for="(row, index) in rows"
      :key="index"
      :direction="direction"
      class="tui-totaraEngage-cardsGrid__row"
    >
      <GridItem
        v-for="(card, i) in row"
        :key="i"
        :units="cardUnits"
        class="tui-totaraEngage-cardsGrid__card"
      >
        <component
          :is="card.component"
          :key="`${card.component}-${card.instanceid}`"
          :instanceid="card.instanceid"
          :name="card.name"
          :summary="card.summary"
          :user="card.user"
          :access="card.access"
          :time-created="card.timeCreated"
          :extra="card.extra"
          :topics="card.topics"
          :rating="card.rating"
          :bookmarked="card.bookmared"
          :total-comments="card.totalComments"
          :total-reactions="card.totalReactions"
          :total-shares="card.sharedbycount"
          :owned="card.owned"
          number-of-people-rated="0"
        />
      </GridItem>
    </CoreGrid>
  </Responsive>
</template>

<script>
import tui from 'tui/tui';
import { AccessManager } from 'totara_engage/index';
import CoreGrid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Responsive from 'tui/components/responsive/Responsive';

const sampleUrl =
  'https://ksassets.timeincuk.net/wp/uploads/sites/55/2019/09/092319-Twice-Feel-Special-JYP-Entertainment-920x584.jpg';

const avatarUrl =
  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTraWBXv4yZXxfZOS8llQbEllO9LIPga92NJHc4iUxRmYHh9NG-&s';

function calculateRow(cards, itemsPerPage) {
  let rows = [];

  for (let index = 0; index < cards.length; index += itemsPerPage) {
    let row = cards.slice(index, index + itemsPerPage);
    rows.push(row);
  }

  return rows;
}

export default {
  components: {
    GridItem,
    ArticleCard: tui.asyncComponent(
      'engage_article/components/card/ArticleCard'
    ),
    PlaylistCard: tui.asyncComponent(
      'totara_playlist/components/card/PlaylistCard'
    ),
    SurveyCard: tui.asyncComponent('engage_survey/components/card/SurveyCard'),
    CoreGrid,
    Responsive,
  },

  data() {
    return {
      cards: [
        {
          instanceid: 1,
          name: 'Hello world resource',
          totalReactions: 15,
          sharedbycount: 10,
          totalComments: 1,
          extra: JSON.stringify({
            image: sampleUrl,
            usage: 5,
          }),
          user: {
            id: 12,
            fullname: 'Bolo bala',
            profileimageurl: '',
            profileimagealt: '',
          },
          access: AccessManager.PRIVATE,
          timeCreated: 'Monday 18th, September, 2019',
          rating: 0,
          component: 'ArticleCard',
        },
        {
          name: 'Hello world',
          instanceid: 2,
          summary: null,
          user: {
            id: 15,
            fullname: 'Bolo bala',
            profileimageurl: '',
            profileimagealt: '',
          },
          access: AccessManager.PUBLIC,
          timeCreated: 'Monday 18th, September, 2019',
          rating: 0,
          totalReactions: 0,
          totalComments: 0,
          sharedbycount: 0,
          extra: JSON.stringify({
            resources: 0,
            actions: false,
            // extraData will cover this by providing default images.
            images: [sampleUrl, sampleUrl, sampleUrl, sampleUrl],
          }),
          component: 'PlaylistCard',
        },
        {
          name: 'Do you know how to Google ???',
          instanceid: 3,
          summary: null,
          user: {
            id: 42,
            fullname: 'Taylor Swift',
            profileimageurl: avatarUrl,
            profileimagealt: '',
          },
          access: AccessManager.PUBLIC,
          timeCreated: 'Monday 18th, September, 2019',
          rating: 0,
          totalReactions: 0,
          totalComments: 0,
          sharedbycount: 0,
          extra: JSON.stringify({
            voted: false,
            expired: null,
            questions: [1],
          }),
          component: 'SurveyCard',
        },
      ],
      cardUnits: 3,
      itemsPerRow: 4,
      size: null,
    };
  },

  computed: {
    rows() {
      return calculateRow(this.cards, this.itemsPerRow);
    },

    direction() {
      switch (this.size) {
        case 's':
          return 'vertical';

        case 'm':
        case 'l':
        default:
          return 'horizontal';
      }
    },
  },

  methods: {
    $_handleResize(name) {
      this.size = name;
      switch (name) {
        case 's':
          this.itemsPerRow = 1;
          this.cardUnits = 11;
          break;

        case 'm':
          this.itemsPerRow = 2;
          this.cardUnits = 5;
          break;

        case 'l':
          this.itemsPerRow = 4;
          this.cardUnits = 3;
          break;
      }
    },
  },
};
</script>
