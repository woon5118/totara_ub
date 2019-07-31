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
  <div class="tui-testStarRating">
    <span class="tui-testStarRating__text">
      Read-only:
    </span>
    <span class="tui-testStarRating__rating1">
      <StarRating :increment="0.1" :read-only="true" :rating="3.7" />
    </span>
    <span class="tui-testStarRating__text">
      Editable:
    </span>
    <span class="tui-testStarRating__rating1">
      <StarRating :increment="0.1" :read-only="false" :rating="0" />
    </span>
    <span class="tui-testStarRating__text">
      Editable with full increment:
    </span>
    <span class="tui-testStarRating__rating1">
      <StarRating :increment="1" :read-only="false" :rating="0" />
    </span>
    <span class="tui-testStarRating__text">
      RTL read-only:
    </span>
    <span class="tui-testStarRating__rating1">
      <StarRating
        :increment="0.1"
        :read-only="true"
        :rating="4.3"
        :rtl="true"
      />
    </span>
    <span class="tui-testStarRating__text">
      RTL editable:
    </span>
    <span class="tui-testStarRating__rating1">
      <StarRating :increment="0.1" :read-only="false" :rating="0" :rtl="true" />
    </span>
    <span class="tui-testStarRating__text">
      Read-only:
    </span>
    <span class="tui-testStarRating__rating2">
      <StarRating
        :increment="0.1"
        :max-rating="3"
        :read-only="true"
        :rating="3.7"
      />
    </span>
    <span class="tui-testStarRating__text">
      Editable:
    </span>
    <span class="tui-testStarRating__rating2">
      <StarRating
        :increment="0.1"
        :max-rating="3"
        :read-only="false"
        :rating="0"
      />
    </span>
  </div>
</template>

<script>
import StarRating from 'totara_engage/components/icons/StarRating';

export default {
  components: {
    StarRating: StarRating,
  },
};
</script>

<style lang="scss">
.tui-testStarRating {
  display: flex;
  flex-direction: column;

  &__text {
    display: inline-flex;
    width: 200px;
    margin-top: 20px;
  }

  &__rating1 {
    display: inline-flex;
    justify-content: center;
    width: 200px;

    .tui-totaraEngage-star {
      width: var(--tui-font-size-22);
      height: var(--tui-font-size-22);

      &__filled {
        stop-color: var(--tui-color-chart-background-4);
      }

      &__unfilled {
        stop-color: var(--tui-color-neutral-4);
      }
    }
  }

  &__rating2 {
    display: inline-flex;
    justify-content: center;
    width: 200px;

    .tui-totaraEngage-star {
      width: var(--tui-font-size-48);
      height: var(--tui-font-size-48);

      &__filled {
        stop-color: var(--tui-color-chart-background-5);
      }

      &__unfilled {
        stop-color: var(--tui-color-neutral-4);
      }
    }
  }

  .tui-totaraEngage-rating {
    margin: var(--tui-gap-1) 0;
  }
}
</style>
