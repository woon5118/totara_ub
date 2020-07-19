module.exports = {
    extends: [
        './scripts/configs/.stylelintrc_tui.js',
        "stylelint-config-prettier"
    ],
    plugins: [
        "stylelint-order",
        "./scripts/stylelint/ascii-only"
    ],
    ignoreFiles: [
        "src/theme_legacy/**"
    ],
    rules: {
        "order/properties-order": require('./scripts/configs/stylelint_order'),
        "tui/ascii-only": true,
    }
}