module.exports = {
  root: true,
  parser: '@typescript-eslint/parser',
  plugins: [
    '@typescript-eslint',
    'typescript-sort-keys',
    'eslint-plugin-react',
    'eslint-plugin-import',
    'eslint-plugin-react-hooks',
    'eslint-plugin-typescript-sort-keys',
  ],
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/eslint-recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:react-hooks/recommended',
  ],
  rules: {
    'react/jsx-sort-props': ['warn', { multiline: 'last' }],
    'import/order': ['warn', { alphabetize: { order: 'asc' } }],
    'typescript-sort-keys/interface': 'warn',
    '@typescript-eslint/no-explicit-any': 'off',
  },
};
