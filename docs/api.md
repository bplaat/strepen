[&laquo; Back to the README.md](../README.md)

# API Documentation
Here is an idea of how the REST API can look like but with Livewire this is not really nessary any more:

```
POST /api/auth/login
GET /api/auth/logout

GET /api/users ADMIN = more info
POST /api/users ADMIN
GET /api/users/{user} ADMIN = more info & your self
GET /api/users/{user}/posts
GET /api/users/{user}/transactions ADMIN & your self
POST /api/users/{user/edit ADMIN & your self
GET /api/users/{user}/delete ADMIN & your self

GET /api/products
POST /api/products ADMIN
GET /api/products/{product}
POST /api/products/{product/edit ADMIN
GET /api/products/{product}/delete ADMIN

GET /api/inventories ADMIN
POST /api/inventories ADMIN
GET /api/inventories/{inventory} ADMIN
POST /api/inventories/{inventory/edit ADMIN
GET /api/inventories/{inventory}/delete ADMIN

GET /api/transactions ADMIN
POST /api/transactions
GET /api/transactions/{transaction} ADMIN & your self
POST /api/inventories/{transaction/edit ADMIN
GET /api/inventories/{transaction}/delete ADMIN

GET /api/posts
POST /api/posts ADMIN
GET /api/posts/{post}
POST /api/posts/{post}/edit ADMIN
GET /api/posts/{post}/delete ADMIN
```
