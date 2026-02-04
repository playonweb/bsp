# Solutions

## Final Decisions (Trade-offs)

1. No separate cities table or migration, overkill for scope.

1. The Engine: MyISAM does not support row-level locking (it locks the whole table for writes), which is a bottleneck for high-concurrency systems.

1. BIGINT for id to future-safe. because BIGINT can store up to 9,223,372,036,854,775,807 rows means 9.2 quintillion rows which is more than enough for any application.

1. Indexing city column, because we are filtering by city.

1. `KEY city_idx (city)` to speed up the query by caching the result of the query.

1. VARCHAR instead of TEXT to limit the size of the column.

1. JQuery and fonts are dependencies of the bootstrap, so I will not remove it because I'm using Tailwind CSS.

1. As there is no restriction on using any libraries, I will use petite-vue and primeicons for better UX & DX.

1. Removed jquery, bootstrap and it's fonts instead using petite-vue, tailwind and primeicons via CDN as a quick-dirty strategy in the future of course it should be compiled and tree shakened.

1. Removed shortcut icon because it is outdated.
`<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />`

1. Why not pagination?
- Since it loads all rows at once, it keeps JS simple and avoids premature pagination.

1. Why Docker & docker-compose?
- To save time in setting up the environment and easily host it over my own home lab.


## Future Scope
What I would improve if I had more time:

1. Integrate all betterstack services: uptime, telemetry, warehouse & error tracking etc.
1. Unit/integration tests, api contracts, ci/cd, cdn etc all common stuff.
1. advanced search, sort & filter options
1. Compile & tree shake tailwind, primeicons & use vuejs instead of petite-vue
1. Captcha & rate limiting for security. MyISAM engine was a bottleneck acting like a dirty rate limiter, now it's InnoDB.
1. More guards against basic attacks CORS, XSS, SQL injections etc.
1. API idempotency management.
1. Find a rhobust strategy to migrate database changes.
1. Anti web scraper strategies. Obfuscate the email and phone number from bots.
