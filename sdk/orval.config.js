module.exports = {
    "sdk": {
        input: {
            target: "../app/docs/openapi.yaml",
        },
        output: {
            baseUrl: '/',
            client: 'fetch',
            mock: false,
            override: {
                useTypes: true,
            },
            target: "./dist/sdk.ts",
        },
    },
};