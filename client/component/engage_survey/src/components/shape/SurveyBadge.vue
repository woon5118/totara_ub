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

  @author Qingyang Liu <Qingyang.liu@totaralearning.com>
  @module engage_survey
-->

<template>
  <svg
    class="tui-engageSurveyBadge"
    viewBox="0 0 105 40"
    version="1.1"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
  >
    <title>{{ $str('survey', 'engage_survey') }}</title>
    <desc>{{ $str('survey', 'engage_survey') }}</desc>
    <g class="tui-engageSurveyBadge__shapeParent">
      <g transform="translate(-91.000000, 0.000000)">
        <g>
          <g transform="translate(91.000000, 0.000000)">
            <path
              class="tui-engageSurveyBadge__shape"
              d="M1.5,33.5 L3.71008242,33.5  L101.289918,33.5 L103.5,33.5 L103.5,6 C103.5,3.51471863 101.485281,1.5 99,1.5 L6,1.5 C3.51471863,1.5 1.5,3.51471863 1.5,6 L1.5,33.5 Z"
            />
            <g
              class="tui-engageSurveyBadge__text"
              transform="translate(30.500000, 7.000000)"
            >
              <g>
                <text>
                  <tspan x="0" y="13">
                    {{ $str('survey', 'engage_survey') }}
                  </tspan>
                </text>
              </g>
            </g>
          </g>
        </g>
      </g>
    </g>
  </svg>
</template>

<lang-strings>
  {
    "engage_survey": [
      "survey"
    ]
  }
</lang-strings>

<style lang="scss">
:root {
  --engageSurvey-badge-height: 40px;
  --engageSurvey-badge-width: 105px;
}

.tui-engageSurveyBadge {
  width: var(--engageSurvey-badge-width);
  height: var(--engageSurvey-badge-height);

  &__text {
    font-weight: bold;
    font-size: 13px;
    fill: #262626;
  }

  &__shapeParent {
    fill: none;
    fill-rule: evenodd;
    stroke: none;
    stroke-width: 1;
  }

  &__shape {
    fill: var(--color-neutral-1);
    stroke: var(--color-primary);
    stroke-width: 3;
  }
}
</style>
