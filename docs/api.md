[&laquo; Back to the README.md](../README.md)

# API Documentation
Here are all the routes of the REST API more information will follow:

```
GET /api GUEST ✅

ANY /api/auth/login GUEST ✅ (email, password)
GET /api/auth/logout AUTH ✅

GET /api/settings AUTH ✅
POST /api/settings ADMIN ❌

GET /api/users AUTH ✅ (query?, page?, limit?)
POST /api/users MANAGER ❌
GET /api/users/check_balances MANAGER ✅
GET /api/users/{user} AUTH ✅
GET /api/users/{user}/notifications SELF ✅ (page?, limit?)
GET /api/users/{user}/notifications/unread SELF ✅ (page?, limit?)
GET /api/users/{user}/posts AUTH ✅ (query?, page?, limit?)
GET /api/users/{user}/inventories MANAGER ✅ (query?, page?, limit?)
GET /api/users/{user}/transactions SELF ✅ (query?, page?, limit?)
POST /api/users/{user/edit SELF ✅ (
    firstname?, insertion?, lastname?, gender?, birthday?, email?, phone?,
    address?, postcode?, city?, language?, theme?, receive_news?,
    avatar?, thanks?,
    current_password?, password?, password_confirmation?
)
GET /api/users/{user}/delete MANAGER ❌

GET /api/notifications/{notification}/read SELF ✅

GET /api/posts AUTH ✅ (query?, page?, limit?)
POST /api/posts MANAGER ❌
GET /api/posts/{post} AUTH ✅
POST /api/posts/{post}/edit MANAGER ❌
GET /api/posts/{post}/delete MANAGER ❌

GET /api/products AUTH ✅ (query?, page?, limit?)
POST /api/products MANAGER ❌
GET /api/products/{product} AUTH ✅
POST /api/products/{product/edit MANAGER ❌
GET /api/products/{product}/delete MANAGER ❌

GET /api/inventories MANAGER ✅ (query?, page?, limit?)
POST /api/inventories MANAGER ❌
GET /api/inventories/{inventory} MANAGER ✅
POST /api/inventories/{inventory/edit MANAGER ❌
GET /api/inventories/{inventory}/delete MANAGER ❌

GET /api/transactions MANAGER ✅ (query?, page?, limit?)
POST /api/transactions AUTH ✅ (user_id?, name, products[] { product_id, amount})
GET /api/transactions/{transaction} SELF ✅
POST /api/inventories/{transaction/edit MANAGER ❌
GET /api/inventories/{transaction}/delete MANAGER ❌
```
