module.exports = {
    root: true,
    env: {
        browser: true,
        es2022: true,
    },
    extends: ['eslint:recommended', 'prettier'],
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
    },
    ignorePatterns: ['public/**', 'vendor/**', 'node_modules/**'],
    rules: {
        'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
    },
};
