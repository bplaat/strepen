[&laquo; Back to the README.md](../README.md)

# API Documentation
Here are all the routes of the REST API more information will follow:

```
GET /api ✅

POST /api/auth/login ✅ (email, password)
GET /api/auth/logout ✅

GET /api/users ADMIN = more info ✅ (query, page, limit)
POST /api/users ADMIN ❌
GET /api/users/check_balances ADMIN ❌
GET /api/users/{user} ADMIN = more info & your self ✅
GET /api/users/{user}/notifications ADMIN & your self ❌
GET /api/users/{user}/notifications/unread ADMIN & your self ❌
GET /api/users/{user}/posts ✅ (query, page, limit)
GET /api/users/{user}/inventories ADMIN ❌
GET /api/users/{user}/transactions ADMIN & your self ❌
POST /api/users/{user/edit ADMIN & your self ❌
GET /api/users/{user}/delete ADMIN & your self ❌

GET /api/notifications/{notification}/read ADMIN & your self ❌

GET /api/posts ✅ (query, page, limit)
POST /api/posts ADMIN ❌
GET /api/posts/{post} ✅
POST /api/posts/{post}/edit ADMIN ❌
GET /api/posts/{post}/delete ADMIN ❌

GET /api/products ✅ (query, page, limit)
POST /api/products ADMIN ❌
GET /api/products/{product} ✅
POST /api/products/{product/edit ADMIN ❌
GET /api/products/{product}/delete ADMIN ❌

GET /api/inventories ADMIN ❌
POST /api/inventories ADMIN ❌
GET /api/inventories/{inventory} ADMIN ❌
POST /api/inventories/{inventory/edit ADMIN ❌
GET /api/inventories/{inventory}/delete ADMIN ❌

GET /api/transactions ADMIN ❌
POST /api/transactions ❌
GET /api/transactions/{transaction} ADMIN & your self ❌
POST /api/inventories/{transaction/edit ADMIN ❌
GET /api/inventories/{transaction}/delete ADMIN ❌
```
