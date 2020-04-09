module.exports = {
  extends: ['./scripts/configs/.eslintrc_tui.js'],
  overrides: [
    {
      files: ['scripts/**/*', 'tests/**/*', '**/__tests__/**/*'],
      rules: {
        'tui/no-object-spread': 'off',
        'tui/no-for-of': 'off',
      },
    },
  ],
};
