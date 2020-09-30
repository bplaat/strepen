const express = require('express');
const mysql2 = require('mysql2/promise');

const app = express();
const PORT = process.env.PORT || 8080;

// #######################################################
// ####################### Utils #########################
// #######################################################

function validateEmail (email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// #######################################################
// ###################### Database #######################
// #######################################################

let db;

async function databaseConnect() {
    db = await mysql2.createPool({
        host: 'localhost',
        user: 'strepen',
        password: 'strepen',
        database: 'strepen',
        charset: 'utf8mb4'
    });
}

databaseConnect();

// #######################################################
// ####################### Models ########################
// #######################################################

class Users {
}

Users.Role = {
    OUD_STAMMER: 0,
    STAM_LID: 1,
    STAM_BESTUURDER: 2
};

// #######################################################
// ##################### Controllers #####################
// #######################################################

// Pages controller
class PagesController {
    static home (req, res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            succes: true,
            message: 'Het nieuwe Strepen Systeem REST API'
        }));
    }

    static notFound (req, res) {
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            succes: false,
            message: '404 Not Found'
        }));
    }
}

// Users controller
class UsersController {
    // Users index route
    static async index (req, res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });

        const users = (await db.query('SELECT `id`, `firstname`, `lastname`, `role`, `created_at`, `updated_at` FROM `users`'))[0];

        res.end(JSON.stringify({
            succes: true,
            users: users
        }));
    }

    // Users search route
    static async search (req, res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });

        const users = (await db.query('SELECT `id`, `firstname`, `lastname`, `role`, `created_at`, `updated_at` FROM `users`' +
            'WHERE `firstname` LIKE ? OR `lastname` LIKE ?', [ '%' + req.query.q + '%', '%' + req.query.q + '%' ]))[0];

            res.end(JSON.stringify({
            succes: true,
            users: users
        }));
    }

    // Users create route
    static async create (req, res) {
        res.writeHead(200, { 'Content-Type': 'application/json' });

        const firstname = req.query.firstname;
        if (firstname == undefined || firstname.length < 2 || firstname.length > 255) {
            res.end(JSON.stringify({ succes: false }));
            return;
        }

        const lastname = req.query.lastname;
        if (lastname == undefined || lastname.length < 3 || lastname.length > 255) {
            res.end(JSON.stringify({ succes: false }));
            return;
        }

        const email = req.query.email;
        if (email == undefined || email.length > 255 || !validateEmail(email)) {
            res.end(JSON.stringify({ succes: false }));
            return;
        }

        try {
            const userId = (await db.query('INSERT INTO `users` (`firstname`, `lastname`, `email`) VALUES (?, ?, ?)',
                [ firstname, lastname, email ]))[0].insertId;

            res.end(JSON.stringify({
                succes: true,
                user_id: userId
            }));
        }

        catch (exception) {
            res.end(JSON.stringify({ succes: false }));
            return;
        }
    }

    static async show (req, res) {

    }

    static async edit (req, res) {

    }

    static async delete (req, res) {

    }
}

// Products controller
class ProductsController {
    static index (req, res) {

    }

    static search (req, res) {

    }

    static create (req, res) {

    }

    static show (req, res) {

    }

    static edit (req, res) {

    }

    static delete (req, res) {

    }
}

// User debt controller
class UserDebtController {
    static create (req, res) {

    }
}

// Product stock controller
class ProductStockController {
    static create (req, res) {

    }
}

// News controller
class NewsController {
    static index (req, res) {

    }

    static search (req, res) {

    }

    static create (req, res) {

    }

    static show (req, res) {

    }

    static edit (req, res) {

    }

    static delete (req, res) {

    }
}

// #####################################################
// ###################### Routes #######################
// #####################################################

// Home
app.all('/', PagesController.home);

// Auth routes

// Users routes
app.all('/api/users', UsersController.index);
app.all('/api/users/search', UsersController.search);
app.all('/api/users/create', UsersController.create);
app.all('/api/users/{user_id}', UsersController.show);
app.all('/api/users/{user_id}/edit', UsersController.edit);
app.all('/api/users/{user_id}/delete', UsersController.delete);

// Product routes
app.all('/api/products', ProductsController.index);
app.all('/api/products/search', ProductsController.search);
app.all('/api/products/create', ProductsController.create);
app.all('/api/products/{product_id}', ProductsController.show);
app.all('/api/products/{product_id}/edit', ProductsController.edit);
app.all('/api/products/{product_id}/delete', ProductsController.delete);

// User debt routes
app.all('/api/users/{user_id}/debt/{product_id}/create', UserDebtController.create);

// Product stock routes
app.all('/api/products/{product_id}/stock/create', ProductStockController.create);

// News routes
app.all('/api/news', NewsController.index);
app.all('/api/news/search', NewsController.search);
app.all('/api/news/create', NewsController.create);
app.all('/api/news/{product_id}', NewsController.show);
app.all('/api/news/{product_id}/edit', NewsController.edit);
app.all('/api/news/{product_id}/delete', NewsController.delete);

// 404 Not Found
app.use(PagesController.notFound);

// Listen
app.listen(PORT, function () {
    console.log('Server is listening on http://localhost:' + PORT + '/');
});
