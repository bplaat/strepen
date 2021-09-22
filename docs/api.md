[&laquo; Back to the README.md](../README.md)

# API Documentation
Here are all the routes of the REST API more information will follow:

```
GET /api GUEST ✅

POST /api/auth/login GUEST ✅ (email, password)
GET /api/auth/logout AUTHED ✅

GET /api/users AUTHED ✅ (query, page, limit)
POST /api/users ADMIN ❌
GET /api/users/check_balances ADMIN ❌
GET /api/users/{user} SELF & ADMIN ✅
GET /api/users/{user}/notifications SELF & ADMIN ❌
GET /api/users/{user}/notifications/unread SELF & ADMIN ❌
GET /api/users/{user}/posts GUEST ✅ (query, page, limit)
GET /api/users/{user}/inventories ADMIN ❌
GET /api/users/{user}/transactions SELF & ADMIN ❌
POST /api/users/{user/edit SELF & ADMIN ❌
GET /api/users/{user}/delete SELF & ADMIN ❌

GET /api/notifications/{notification}/read SELF & ADMIN ❌

GET /api/posts GUEST ✅ (query, page, limit)
POST /api/posts ADMIN ❌
GET /api/posts/{post} GUEST ✅
POST /api/posts/{post}/edit ADMIN ❌
GET /api/posts/{post}/delete ADMIN ❌

GET /api/products AUTHED ✅ (query, page, limit)
POST /api/products ADMIN ❌
GET /api/products/{product} AUTHED ✅
POST /api/products/{product/edit ADMIN ❌
GET /api/products/{product}/delete ADMIN ❌

GET /api/inventories ADMIN ❌
POST /api/inventories ADMIN ❌
GET /api/inventories/{inventory} ADMIN ❌
POST /api/inventories/{inventory/edit ADMIN ❌
GET /api/inventories/{inventory}/delete ADMIN ❌

GET /api/transactions ADMIN ❌
POST /api/transactions AUTHED ❌
GET /api/transactions/{transaction} SELF & ADMIN ❌
POST /api/inventories/{transaction/edit ADMIN ❌
GET /api/inventories/{transaction}/delete ADMIN ❌
```
