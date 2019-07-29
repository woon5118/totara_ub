module.exports = {
  extends: ['./scripts/configs/.eslintrc_tui.js'],
  overrides: [
    {
      files: ['scripts/**/*', 'tests/**/*'],
      rules: {
        'tui/no-object-spread': 'off',
        'tui/no-for-of': 'off',
      },
    },
  ],
};
