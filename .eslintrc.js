module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: [
    'plugin:react/recommended',
    'airbnb',
  ],
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    ecmaVersion: 12,
    sourceType: 'module',
  },
  plugins: [
    'react',
  ],
  rules: {
    'jsx-a11y/media-has-caption': 'off',
    'max-len': ['warn', { code: 160 }],
    'no-multiple-empty-lines': 'off',
    'react/jsx-no-target-blank': 'off',
    'react/jsx-props-no-spreading': ['warn', { html: 'ignore' }],
  },
};
