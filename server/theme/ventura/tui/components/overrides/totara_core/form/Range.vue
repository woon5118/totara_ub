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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
@mixin tui-range-track() {
  height: 10px;
  background: var(--tui-color-neutral-4);
  border-radius: 4px;
  cursor: pointer;
}
@mixin tui-range-thumb() {
  width: 18px;
  height: 18px;
  background: var(--tui-color-neutral-5);
  border: none;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
  cursor: pointer;
}

.tui-range {
  flex: auto;
  flex-direction: column;

  &__labels {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: var(--tui-gap-1);

    > * {
      @include tui-font-body-small;
      color: var(--tui-color-neutral-6);
    }
  }

  &__lowLabel {
    flex-basis: 40%;
    text-align: left;
  }

  &__highLabel {
    flex-basis: 40%;
    text-align: right;
  }

  &__input {
    height: 20px;
    padding: 0;
    outline: none;
  }

  input[type='range'] {
    -webkit-appearance: none;
  }

  input[type='range']:disabled {
    background-color: transparent;
  }

  /* Hide outline border */
  input[type='range']:focus {
    outline: none;
  }
  input[type='range']::-moz-focus-outer {
    border: 0;
  }

  /* Track styles */
  input[type='range']::-webkit-slider-runnable-track {
    @include tui-range-track;
  }
  input[type='range']:focus::-webkit-slider-runnable-track {
    background: var(--tui-color-neutral-4);
  }
  input[type='range']::-moz-range-track {
    @include tui-range-track;
  }
  input[type='range']::-ms-track {
    @include tui-range-track;
    color: transparent; /* Remove default tick marks */
    background: transparent; /* Replace bg colour from the track with ms-fill-lower and ms-fill-upper */
    border-color: transparent; /* Thumb can not overlay track so we add invisible border */
    border-top-width: 4px;
    border-bottom-width: 6px;
  }
  input[type='range']::-ms-fill-upper {
    background: rgb(230, 228, 228);
    border-radius: 4px;
  }
  input[type='range']::-ms-fill-lower {
    background: rgb(230, 228, 228);
    border-radius: 4px;
  }
  input[type='range']:focus::-ms-fill-upper {
    background: rgb(230, 228, 228);
  }
  input[type='range']:focus::-ms-fill-lower {
    background: rgb(230, 228, 228);
  }

  /* Thumb styles */
  input[type='range']::-webkit-slider-thumb {
    @include tui-range-thumb;
    margin-top: -5px;
    -webkit-appearance: none;
  }
  input[type='range']::-moz-range-thumb {
    @include tui-range-thumb;
  }
  input[type='range']::-ms-thumb {
    @include tui-range-thumb;
    margin-top: 2px;
    background: rgb(199, 199, 199);
  }

  input[type='range'].tui-range__input--selected::-webkit-slider-thumb {
    background: var(--tui-color-state);
  }
  input[type='range'].tui-range__input--selected::-moz-range-thumb {
    background: var(--tui-color-state);
  }
  input[type='range'].tui-range__input--selected::-ms-thumb {
    background: rgb(75, 126, 43);
  }
}
</style>
