## System Design


## Decisions
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