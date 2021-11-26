[&laquo; Back to the README.md](../README.md)

# API Documentation
Here are all the routes of the REST API more information will follow:

```
GET /api GUEST ✅

ANY /api/auth/login GUEST ✅ (email: string, password: string)
GET /api/auth/logout AUTH ✅

GET /api/settings AUTH ✅
POST /api/settings ADMIN ❌

GET /api/users AUTH ✅ (query: string?, page: int?, limit: int?)
POST /api/users MANAGER ❌
GET /api/users/check_balances MANAGER ✅
GET /api/users/{user} AUTH ✅
GET /api/users/{user}/notifications SELF ✅ (page: int?, limit: int?)
GET /api/users/{user}/notifications/unread SELF ✅ (page: int?, limit: int?)
GET /api/users/{user}/posts AUTH ✅ (query: string?, page: int?, limit: int?)
GET /api/users/{user}/inventories MANAGER ✅ (query: string?, page: int?, limit: int?)
GET /api/users/{user}/transactions SELF ✅ (query: string?, page: int?, limit: int?)
POST /api/users/{user}/edit SELF ✅ (
    firstname: string?, insertion: string?, lastname: string?,
    gender: string?, birthday: string?, email: string?, phone: string?,
    address: string?, postcode: string?, city: string?, language: string?,
    theme: string?, receive_news: boolean?,
    avatar: file?, thanks: file?,
    current_password: string?, password: string?, password_confirmation: string?
)
GET /api/users/{user}/delete MANAGER ❌

GET /api/notifications/{notification}/read SELF ✅

GET /api/posts AUTH ✅ (query: string?, page: int?, limit: int?)
POST /api/posts MANAGER ❌
GET /api/posts/{post} AUTH ✅
POST /api/posts/{post}/edit MANAGER ❌
GET /api/posts/{post}/delete MANAGER ❌

GET /api/products AUTH ✅ (query: string?, page: int?, limit: int?)
POST /api/products MANAGER ❌
GET /api/products/{product} AUTH ✅
POST /api/products/{product/edit MANAGER ❌
GET /api/products/{product}/delete MANAGER ❌

GET /api/inventories MANAGER ✅ (query: string?, page: int?, limit: int?)
POST /api/inventories MANAGER ❌
GET /api/inventories/{inventory} MANAGER ✅
POST /api/inventories/{inventory/edit MANAGER ❌
GET /api/inventories/{inventory}/delete MANAGER ❌

GET /api/transactions MANAGER ✅ (query: string?, page: int?, limit: int?)
POST /api/transactions AUTH ✅ (user_id: int?, name: string,
    products[#][product_id]: int, products[#][amount]: int)
GET /api/transactions/{transaction} SELF ✅
POST /api/inventories/{transaction/edit MANAGER ❌
GET /api/inventories/{transaction}/delete MANAGER ❌
```
