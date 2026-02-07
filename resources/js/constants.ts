import { Mask } from 'maska';

export const colors = [
    // red
    '#E53A36',
    // pink
    '#D71A60',
    // violet
    '#8E24AA',
    // purple
    '#7F57C2',
    // marine
    '#283593',
    // lime - grey
    '#00C852',
    '#757575',
    // blue
    '#1D89E5',
    // sea
    '#00897A',
    // cyan
    '#00ACC2',
    // green
    '#18A05F',
    '#7DB342',
    // orange
    '#FB8C00',
    '#EF6C02',
    // light blue
    '#039BE6',
    // green-yellow
    '#C0CA33',
    // yellow
    '#FFB300',
    // red-orange
    '#D74214',
    // brown
    '#6D4C42',
    // grey
    '#546F79',
];

export const currencyMask = new Mask({
    mask: '9.99#,##',
    reversed: true,
    tokens: {
        9: {
            pattern: /[0-9]/,
            repeated: true,
        },
    },
});
