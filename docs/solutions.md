## Identified Problems

Since the given challenge is pretty vague, open and it has numerous possible solutions and I am not sure which one is expected or not expected. So I hope the examiner will also score it with open mind and not have any already fixed existing solutions in their mind they are waiting for other than the expectations which are clearly written.

- The Engine: MyISAM does not support row-level locking (it locks the whole table for writes), which is a bottleneck for high-concurrency systems.
- Must replace MyISAM with InnoDB for production ready.
- Column Types: Using TEXT for name, email, and city prevents you from using standard B-Tree indexing effectively.


## System Design



## Final Decisions

### Database Decisions

1. Why estimate system design for 2.1B means more than average population of a country, no duplicates, no spams.
Given schema has int(11) means 2,147,483,647 signed int means 2.1 Billion rows max, since it's city filter not zip, country so maybe its limited to one country and 2.1B is more than enough for most of the countries.

1. Why InnoDB instead of MyISAM
MyISAM means maybe it's a private app for only one person but maybe interviewer is expecting me to fix it and make it InnoDB which is impossible for me to guess but to be on the safe side it's better to give optimal solution so I will use InnoDB.

1. Why indexing city column
column `city` text can't do B-tree indexing well for filter by city which is required.

### Security Decisions

1. Why added [app/config/routes.php](../app/config/routes.php) and [app/core/router.php](../app/core/router.php) to limit the access.
1. 



### Frontend Decisions

1. Removed jquery, bootstrap and it's fonts instead using petite-vue, tailwind and primeicons via CDN as a quick-dirty strategy in the future of course it should be compiled and tree shakened.

1. Removed shortcut icon because it is outdated.
`<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />

1. Why to add security maybe csrf/sql injection maybe simple auth, idempotency, simple captcha etc

1. Why pagination


## Future Scope

1. Add uptime link
2. compile & tree shake tailwind, primeicons & use vuejs instead of petite-vue
3. add CF tunnel auth `@betterstack.com` only logins
