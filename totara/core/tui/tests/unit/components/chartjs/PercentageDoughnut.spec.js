/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_core
 */

import { mount } from '@vue/test-utils';
import component from 'totara_core/components/chartjs/PercentageDoughnut.vue';
import ChartJs from 'totara_core/components/chartjs/ChartJs';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);

let wrapper;

// We are mocking theme to override css variables
jest.mock('totara_core/theme', () => {
  return {
    getVar(name) {
      let vars = {
        'tui-color-chart-background-1': '#3869B1',
        'tui-font-size-chart-doughnut-label': '24',
        'tui-font-size-chart-doughnut-percentage-label': '40',
        'tui-color-chart-doughnut-label': '#000',
        'tui-color-neutral-4': '#e6e4e4',
      };
      return vars[name];
    },
  };
});

describe('presentation/chartjs/PercentageDoughnut.vue', () => {
  beforeAll(() => {
    wrapper = mount(component, {
      propsData: {
        percentage: 75,
        header: 'I am a big percentage doughnut',
      },
    });
  });

  it('renders correctly', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  it('initializes correct defaults for charts component', () => {
    let data = {
      datasets: [
        {
          data: [75, 25],
          borderWidth: 2,
          borderColor: ['#3869B1', '#e6e4e4'],
          backgroundColor: ['#3869B1', '#e6e4e4'],
          hoverBackgroundColor: ['#3869B1', '#e6e4e4'],
          hoverBorderColor: ['#3869B1', '#e6e4e4'],
        },
      ],
    };

    let options = {
      cutoutPercentage: 85,
      tooltips: {
        enabled: false,
      },
      plugins: {
        doughnutlabel: {
          labels: [
            {
              text: '75%',
              font: {
                size: 40,
                weight: 'bold',
              },
              color: '#000',
            },
          ],
        },
      },
      legend: {
        display: false,
      },
    };

    expect(wrapper.find(ChartJs).props('options')).toMatchObject(options);
    expect(wrapper.find(ChartJs).props('data')).toMatchObject(data);
    expect(wrapper.find(ChartJs).props('header')).toMatch(
      'I am a big percentage doughnut'
    );
    expect(wrapper.find(ChartJs).props('ariaLabel')).toMatch('75%');
  });

  it('passes correct properties to the chart component', () => {
    let doughnut = mount(component, {
      propsData: {
        percentage: 57,
        header: 'I am a big percentage doughnut!!',
        square: true,
        color: 'yellow',
        backgroundColor: '#123456',
        labelColor: '#654321',
        label: 'I am a label',
        labelFontSize: 16,
        percentageFontSize: 32,
        cutout: 80,
      },
    });

    let data = {
      datasets: [
        {
          data: [57, 43],
          borderWidth: 2,
          borderColor: ['yellow', '#123456'],
          backgroundColor: ['yellow', '#123456'],
          hoverBackgroundColor: ['yellow', '#123456'],
          hoverBorderColor: ['yellow', '#123456'],
        },
      ],
    };

    let options = {
      cutoutPercentage: 80,
      tooltips: {
        enabled: false,
      },
      plugins: {
        doughnutlabel: {
          labels: [
            {
              text: '57%',
              font: {
                size: 32,
                weight: 'bold',
              },
              color: '#654321',
            },
            {
              text: 'I am a label',
              font: {
                size: 16,
              },
              color: '#654321',
            },
          ],
        },
      },
      legend: {
        display: false,
      },
      layout: {
        padding: {
          left: 0,
          right: 0,
          top: 0,
          bottom: 0,
        },
      },
    };

    expect(doughnut.find(ChartJs).props('canvasAttributes')).toMatchObject({
      width: '100%',
      height: '100%',
    });
    expect(doughnut.find(ChartJs).props('options')).toMatchObject(options);
    expect(doughnut.find(ChartJs).props('data')).toMatchObject(data);
    expect(doughnut.find(ChartJs).props('header')).toMatch(
      'I am a big percentage doughnut!!'
    );
    expect(doughnut.find(ChartJs).props('ariaLabel')).toMatch('57%');
  });

  it('does not include second value in the dataset if 100% passed', () => {
    let doughnut = mount(component, {
      propsData: {
        percentage: 100,
        header: 'I am a big percentage doughnut!!',
      },
    });
    let data = {
      datasets: [
        {
          data: [100],
          borderWidth: 2,
          borderColor: ['#3869B1'],
          backgroundColor: ['#3869B1'],
          hoverBackgroundColor: ['#3869B1'],
          hoverBorderColor: ['#3869B1'],
        },
      ],
    };

    let options = {
      cutoutPercentage: 85,
      tooltips: {
        enabled: false,
      },
      plugins: {
        doughnutlabel: {
          labels: [
            {
              text: '100%',
              font: {
                size: 40,
                weight: 'bold',
              },
              color: '#000',
            },
          ],
        },
      },
      legend: {
        display: false,
      },
    };

    expect(doughnut.find(ChartJs).props('options')).toMatchObject(options);
    expect(doughnut.find(ChartJs).props('data')).toMatchObject(data);
    expect(doughnut.find(ChartJs).props('header')).toMatch(
      'I am a big percentage doughnut'
    );
    expect(doughnut.find(ChartJs).props('ariaLabel')).toMatch('100%');
  });

  it('should not have any accessibility violations', async () => {
    const results = await axe(wrapper.element, {
      rules: {
        region: { enabled: false },
      },
    });
    expect(results).toHaveNoViolations();
  });
});
