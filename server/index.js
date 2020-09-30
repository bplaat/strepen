const http = require('http');
const url = require('url');
const mysql2 = require('mysql2/promise');

const PORT = process.env.PORT || 8080;

let db;
async function databaseConnect() {
    db = await mysql2.createPool({
        host: 'localhost',
        user: 'strepen',
        password: 'strepen',
        database: 'strepen',
        charset: 'utf8mb4'
    });

    console.log('[DB] Connected to the database');
}
databaseConnect();

const server = http.createServer(async function (req, res) {
    const pathname = url.parse(req.url).pathname;

    console.log('[WEB] ' + pathname);

    // Home (/)
    if (pathname == '/') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            succes: true,
            message: 'Het nieuwe Strepen Systeem REST API'
        }));
        return;
    }

    // Auth routes
    if (pathname == '/api/auth/login') {

    }

    // Users
    if (pathname == '/api/users') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        const [users] = await db.query('SELECT `id`, `firstname`, `lastname`, `role`, `created_at`, `updated_at` FROM `users`');
        res.end(JSON.stringify({
            succes: true,
            users: users
        }));
        return;
    }

    if (pathname == '/api/users/create') {

    }

    if (pathname == '/api/users/{user_id}') {

    }

    if (pathname == '/api/users/{user_id}/edit') {

    }

    if (pathname == '/api/users/{user_id}/delete') {

    }

    // Products
    if (pathname == '/api/products') {

    }

    if (pathname == '/api/products/create') {

    }

    if (pathname == '/api/products/{product_id}') {

    }

    if (pathname == '/api/products/{product_id}/edit') {

    }

    if (pathname == '/api/products/{product_id}/delete') {

    }

    // Add debt
    if (pathname == '/api/users/{user_id}/debt/{product_id}?amount=100') {

    }

    // Add stock
    if (pathname == '/api/products/{product_id}/stock?user_id=?&amount=100') {

    }

    // News
    if (pathname == '/api/news') {

    }

    if (pathname == '/api/news/create') {

    }

    if (pathname == '/api/news/{news_id}') {

    }

    if (pathname == '/api/news/{news_id}/edit') {

    }

    if (pathname == '/api/news/{news_id}/delete') {

    }

    // 404 Not Found
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
        succes: false,
        message: '404 Not Found'
    }));
});

console.log('[WEB] Server is listening on http://localhost:' + PORT + '/')
server.listen(PORT);
