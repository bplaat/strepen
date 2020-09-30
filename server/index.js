const http = require('http');
const url = require('url');

const PORT = process.env.PORT || 8080;

const server = http.createServer(function (req, res) {
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
    if (pathname == '/auth/login') {

    }

    // Users
    if (pathname == '/users') {

    }

    if (pathname == '/users/create') {

    }

    if (pathname == '/users/{user_id}') {

    }

    if (pathname == '/users/{user_id}/edit') {

    }

    if (pathname == '/users/{user_id}/delete') {

    }

    // Products
    if (pathname == '/products') {

    }

    if (pathname == '/products/create') {

    }

    if (pathname == '/products/{product_id}') {

    }

    if (pathname == '/products/{product_id}/edit') {

    }

    if (pathname == '/products/{product_id}/delete') {

    }

    // Add debt
    if (pathname == '/users/{user_id}/debt/{product_id}?amount=100') {

    }

    // Add stock
    if (pathname == '/products/{product_id}/stock?user_id=?&amount=100') {

    }

    // News
    if (pathname == '/news') {

    }

    if (pathname == '/news/create') {

    }

    if (pathname == '/news/{news_id}') {

    }

    if (pathname == '/news/{news_id}/edit') {

    }

    if (pathname == '/news/{news_id}/delete') {

    }

    // 404 Not Found
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
        succes: false,
        message: '404 Not Found'
    }));
});

console.log('Server is listening on http://localhost:' + PORT + '/')
server.listen(PORT);
