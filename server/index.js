// Load libraries
const express = require('express');
const mysql2 = require('mysql2/promise');
const bcrypt = require('bcrypt');

// Create express app
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

class Model {

}

class Users extends Model {

}

class Sessions extends Model {

}

class Products extends Model {

}

class ProductStock extends Model {

}

class UserDebt extends Model {

}

class Keys extends Model {

}

class News extends Model {

}

// #######################################################
// ##################### Controllers #####################
// #######################################################

class Controller {

}

// Pages controller
class PagesController extends Controller {
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
class UsersController extends Controller {
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

// Sessions controller
class SessionsController extends Controller {
    static index (req, res) {

    }

    static show (req, res) {

    }

    static delete (req, res) {

    }
}

// User sessions controller
class UserSessionsController extends Controller {
    static index (req, res) {

    }
}

// Products controller
class ProductsController extends Controller {
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

// Stock controller
class StockController extends Controller {
    static index (req, res) {

    }

    static show (req, res) {

    }
}

// Product stock controller
class ProductStockController extends Controller {
    static index (req, res) {

    }

    static create (req, res) {

    }
}

// Debt controller
class DebtController extends Controller {
    static index (req, res) {

    }

    static show (req, res) {

    }
}

// User debt controller
class UserDebtController extends Controller {
    static index (req, res) {

    }

    static create (req, res) {

    }
}

// Keys controller
class KeysController extends Controller {
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

// News controller
class NewsController extends Controller {
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
app.all('/auth/login', AuthController.login);
app.all('/auth/logout', AuthController.logout);

// Users routes
app.all('/api/users', UsersController.index);
app.all('/api/users/search', UsersController.search);
app.all('/api/users/{user_id}', UsersController.show);
app.all('/api/admin/users/create', UsersController.create);
app.all('/api/users/{user_id}/edit', UsersController.edit); // Rights
app.all('/api/users/{user_id}/delete', UsersController.delete); // Rights

// Session routes
app.all('/api/admin/sessions', SessionsController.index);
app.all('/api/sessions/{session_id}', SessionsController.show);
app.all('/api/sessions/{session_id}/delete', SessionsController.delete);

// User Sessions routes
app.all('/api/users/{user_id}/sessions', UserSessionsController.index); // Rights

// Product routes
app.all('/api/products', ProductsController.index);
app.all('/api/products/search', ProductsController.search);
app.all('/api/products/{product_id}', ProductsController.show);
app.all('/api/admin/products/create', ProductsController.create);
app.all('/api/admin/products/{product_id}/edit', ProductsController.edit);
app.all('/api/admin/products/{product_id}/delete', ProductsController.delete);

// Stock routes
app.all('/api/admin/stock', StockController.index);
app.all('/api/admin/stock/{stock_id}', StockController.show);

// Product stock routes
app.all('/api/admin/products/{product_id}/stock', ProductStockController.index);
app.all('/api/admin/products/{product_id}/stock/create', ProductStockController.create);

// Debt routes
app.all('/api/admin/debt', DebtController.index);
app.all('/api/debt/{debt_id}', DebtController.show);

// User Debt routes
app.all('/api/users/{user_id}/debt', UserDebtController.index); // Rights
app.all('/api/users/{user_id}/debt/{product_id}/create', UserDebtController.create); // Rights

// Keys routes
app.all('/api/admin/keys', KeysController.index);
app.all('/api/admin/keys/search', KeysController.search);
app.all('/api/admin/keys/{key_id}', KeysController.show);
app.all('/api/admin/keys/create', KeysController.create);
app.all('/api/admin/keys/{key_id}/edit', KeysController.edit);
app.all('/api/admin/keys/{key_id}/delete', KeysController.delete);

// News routes
app.all('/api/news', NewsController.index);
app.all('/api/news/search', NewsController.search);
app.all('/api/news/{news_id}', NewsController.show);
app.all('/api/admin/news/create', NewsController.create);
app.all('/api/admin/news/{news_id}/edit', NewsController.edit);
app.all('/api/admin/news/{news_id}/delete', NewsController.delete);

// 404 Not Found
app.use(PagesController.notFound);

// Listen server
app.listen(PORT, function () {
    console.log('Server is listening on http://localhost:' + PORT + '/');
});
