## Identified Problems

Since the given challenge is pretty vague, open and it has numerous possible solutions and I am not sure which one is expected or not expected. So I hope the examiner will also score it with open mind and not have any already fixed existing solutions in their mind they are waiting for other than the expectations which are clearly written.

- The Engine: MyISAM does not support row-level locking (it locks the whole table for writes), which is a bottleneck for high-concurrency systems.
- Must replace MyISAM with InnoDB for production ready.
- Column Types: Using TEXT for name, email, and city prevents you from using standard B-Tree indexing effectively.


## System Design


## Final Decisions
#### Database Decisions
1. MyISAM vs InnoDB
2. Indexing city column


#### Security Decisions

1. Added [app/config/routes.php](../app/config/routes.php) and [app/core/router.php](../app/core/router.php) to limit the access.
1. 



#### Frontend Decisions

1. Removed jquery, bootstrap and it's fonts instead using petite-vue, tailwind and primeicons via CDN as a quick-dirty strategy in the future of course it should be compiled and tree shakened.

1. Removed shortcut icon because it is outdated.
```html
<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
```