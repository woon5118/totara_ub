module.exports = {
  singleQuote: true,
  trailingComma: 'es5',
  tabWidth: 2,
  overrides: [
    {
      files: "*.graphqls",
      options: {
        parser: "graphql"
      }
    }
  ]
};