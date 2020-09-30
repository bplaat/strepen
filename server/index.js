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

    // 404 Not Found
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
        succes: false,
        message: '404 Not Found'
    }));
});

console.log('Server is listening on http://localhost:' + PORT + '/')
server.listen(PORT);
