module.exports = {
    extends: [
        './tooling/configs/.stylelintrc_tui.js',
        "stylelint-config-prettier"
    ],
    plugins: [
        "stylelint-order",
        "./tooling/stylelint/ascii-only"
    ],
    ignoreFiles: [
        "src/theme_legacy/**"
    ],
    rules: {
        "order/properties-order": require('./tooling/configs/stylelint_order'),
        "tui/ascii-only": true,
    }
}