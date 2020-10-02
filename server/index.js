// Load libraries
const express = require('express');
const morgan = require('morgan');
const mysql2 = require('mysql2/promise');
const bcrypt = require('bcrypt');

// Constants
const PORT = process.env.PORT || 8080;
const PASSWORD_SALT_ROUNDS = 10;

// Create express app
const app = express();
app.use(express.urlencoded({ extended: false }));
app.use(express.json());
if (process.env.NODE_ENV == 'production') {
    app.use(morgan('combined'));
} else {
    app.use(morgan('dev'));
}

// #######################################################
// ######################## Utils ########################
// #######################################################

// Add date to mysql string function to Date class
Date.prototype.toMysqlString = function() {
    return this.getUTCFullYear() + '-' + ((this.getUTCMonth() + 1) + '').padStart(2, '0') + '-' + (this.getUTCDate() + '').padStart(2, '0') + ' ' +
        (this.getUTCHours() + '').padStart(2, '0') + ':' + (this.getUTCMinutes() + '').padStart(2, '0') + ':' + (this.getUTCSeconds() + '').padStart(2, '0');
};

// #######################################################
// ##################### Validation ######################
// #######################################################

function validate (keys) {
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    return async function (req, res, next) {
        const errors = [];
        for (const key in keys) {
            const value = req.params[key] || req.body[key] || req.query[key] || '';
            for (const rule of keys[key]) {
                if (typeof rule != 'string') {
                    const error = await rule(key, value);
                    if (typeof error == 'string') {
                        errors.push(error);
                    }
                }

                else {
                    const parts = rule.split(':');
                    const name = parts[0];
                    const args = (parts[1] || '').split(',');

                    if (name == 'required' && value == '') {
                        errors.push('The ' + key + ' value is required');
                    }
                    if (name == 'int' && isNaN(value) && value != Math.floor(value)) {
                        errors.push('The ' + key + ' value must be a rounded number');
                    }
                    if (name == 'float' && isNaN(value)) {
                        errors.push('The ' + key + ' value must be a number');
                    }
                    if (name == 'email' && !validateEmail(value)) {
                        errors.push('The ' + key + ' value must be an valid email address');
                    }
                    if (name == 'number_min' && value < args[0]) {
                        errors.push('The ' + key + ' value must be at least '  + args[0] + ' or higher');
                    }
                    if (name == 'number_max' && value > args[0]) {
                        errors.push('The ' + key + ' value must be a maximum of '  + args[0] + ' or lower');
                    }
                    if (name == 'number_between' && value < args[0] && value > args[1]) {
                        errors.push('The ' + key + ' value must be between '  + args[0] + ' and ' . $args[1]);
                    }
                    if (name == 'confirmed' && value != (req.params[key + '_confirmation'] || req.body[key + '_confirmation'] || req.query[key + '_confirmation'] || '')) {
                        errors.push('The ' + key + ' values must be the same');
                    }
                    if (name == 'min' && value.length < args[0]) {
                        errors.push('The ' + key + ' value must be at least '  + args[0] + ' characters long');
                    }
                    if (name == 'max' && value.length > args[0]) {
                        errors.push('The ' + key + ' value can be a maximum of '  + args[0] + ' characters');
                    }
                    if (name == 'size' && value.length != args[0]) {
                        errors.push('The ' + key + ' value must be '  + args[0] + ' characters long');
                    }
                    if (name == 'same' && value != (req.params[args[0]] || req.body[args[0]] || req.query[args[0]] || '')) {
                        errors.push('The ' + key + ' value must be the same as the '  + args[0] + ' value');
                    }
                    if (name == 'different' && value == (req.params[args[0]] || req.body[args[0]] || req.query[args[0]] || '')) {
                        errors.push('The ' + key + ' value must be different as the '  + args[0] + ' value');
                    }
                    // if (name == 'exists' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => value ])->rowCount() != 1) {
                    //     errors.push('The ' + key + ' value must refer to something that exists';
                    // }
                    // if (name == 'unique' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => value ])->rowCount() != 0) {
                    //     errors.push('The ' + key + ' value must be unqiue';
                    // }
                }
            }
        }

        if (errors.length != 0) {
            res.json({
                success: false,
                errors: errors
            });
        } else {
            next();
        }
    };
}

// #######################################################
// ###################### Database #######################
// #######################################################

let db;

function databaseConnect() {
    try {
        db = mysql2.createPool({
            host: 'localhost',
            user: 'strepen',
            password: 'strepen',
            database: 'strepen',
            charset: 'utf8mb4'
        });

        console.log('Connect to the MySQL database');
    } catch (exception) {
        console.log('[ERROR] Can\'t connect to the MySQL database');
        process.exit(1);
    }
}

databaseConnect();

// #######################################################
// ####################### Models ########################
// #######################################################

// Base model class
class Model {

}

class Users extends Model {

    static async select (userId) {
        if (userId != undefined) {
            return (await db.query('SELECT `id`, `firstname`, `lastname`, `admin`, `active`, `created_at`, `updated_at` FROM `users` WHERE `id` = ?', [ userId ]))[0][0];
        } else {
            return (await db.query('SELECT `id`, `firstname`, `lastname`, `admin`, `active`, `created_at`, `updated_at` FROM `users`'))[0];
        }
    }

    static async search (query) {
        return (await db.query('SELECT `id`, `firstname`, `lastname`, `admin`, `active`, `created_at`, `updated_at` FROM `users`' +
            'WHERE `firstname` LIKE ? OR `lastname` LIKE ?', [ '%' + query + '%', '%' + query + '%' ]))[0];
    }

    static async insert (firstname, lastname, email) {
        return (await db.query('INSERT INTO `users` (`firstname`, `lastname`, `email`) VALUES (?, ?, ?)', [ firstname, lastname, email ]))[0].insertId;
    }

    static validateOldPassword (name, value) {

    }

    static validateAdmin (name, value) {

    }
}

Users.ID_VALIDATION = [ 'required', 'int', 'exists:Users,id' ];
Users.AUTH_ID_VALIDATION = []; // TODO
Users.FIRSTNAME_VALIDATION = [ 'required', 'min:2', 'max:32' ];
Users.LASTNAME_VALIDATION = [ 'required', 'min:3', 'max:64' ];
Users.QUERY_VALIDATION = [ 'required', ' min:2' ];
Users.EMAIL_VALIDATION = [ 'required', 'email', 'max:255' ];
Users.OLD_PASSWORD_VALIDATION = [ 'required', Users.validateOldPassword ];
Users.PASSWORD_VALIDATION = [ 'required', 'min:6' ];
Users.SESSION_AMIN_VALIDATION = [ 'required', Users.validateAdminSession ];
Users.ACTIVE_VALIDATION = [ 'required', 'boolean' ];

class Sessions extends Model {
    static validateSession (name, value) {

    }
}

Sessions.AUTH_ID_VALIDATION = []; // TODO
Sessions.SESSION_VALIDATION = [ 'required', Sessions.validateSession ];

class Products extends Model {

}

class ProductStock extends Model {

}

class UserDebt extends Model {

}

class Keys extends Model {
    static async validateKey (name, value) {
        // Check API key exists
        const key = (await db.query('SELECT `active` FROM `keys` WHERE `key` = ?', [ value ]))[0][0];
        if (key == undefined) {
            return 'This API key don\'t exists';
        }

        // Check of API key is active
        if (!key.active) {
            return 'This API key isn\'t active';
        }
    }
}

// Only check API key in production
if (process.env.NODE_ENV == 'production') {
    Keys.KEY_VALIDATION = [ 'required', Keys.validateKey ];
} else {
    Keys.KEY_VALIDATION = [];
}

class News extends Model {

}

// #######################################################
// ##################### Controllers #####################
// #######################################################

// Base contoller class
class Controller {

}

// Auth controller
class AuthController extends Controller {
    // Auth login route
    static login (req, res) {

    }

    // Auth logout route
    static logout (req, res) {

    }
}

// Pages controller
class PagesController extends Controller {
    // Pages home route
    static home (req, res) {
        res.json({
            succes: true,
            message: 'Het nieuwe Strepen Systeem REST API'
        });
    }

    // Pages not found route
    static notFound (req, res) {
        res.status(404);
        res.json({
            succes: false,
            message: '404 Not Found'
        });
    }
}

// Users controller
class UsersController extends Controller {
    // Users index route
    static async index (req, res) {
        const users = await Users.select();

        res.json({
            succes: true,
            users: users
        });
    }

    // Users search route
    static async search (req, res) {
        const query = req.body.query ||  req.query.query;

        const users = await Users.search(query);

        res.json({
            succes: true,
            users: users
        });
    }

    // Users create route
    static async create (req, res) {
        const authedUserId = Auth.id();
        const firstname = req.body.firstname || req.query.firstname;
        const lastname = req.body.lastname || req.query.lastname;
        const email = req.body.email || req.query.email;

        const userId = await Users.insert({
            user_id: authedUserId,
            firstname: firstname,
            lastname: lastname,
            email: email
        });
        const user = await Users.select(userId);

        res.json({
            succes: true,
            user: user
        });
    }

    // Users show route
    static async show (req, res) {
        const userId = req.params.user_id;

        const user = await Users.select(userId);

        res.json({
            succes: true,
            user: user
        });
    }

    // Users edit route
    static async edit (req, res) {
        const userId = req.params.user_id;
        const firstname = req.body.firstname || req.query.firstname;
        const lastname = req.body.lastname || req.query.lastname;
        const email = req.body.email || req.query.email;
        const active = req.body.active || req.query.active;

        await Users.update(userId, {
            firstname: firstname,
            lastname: lastname,
            email: email,
            active: active
        });

        res.json({
            succes: true
        });
    }

    // Users edit password route
    static async editPassword (req, res) {
        const userId = req.params.user_id;
        const password = req.body.password || req.query.password;

        await Users.update(userId, {
            password: bcrypt.hashSync(password, PASSWORD_SALT_ROUNDS)
        });

        res.json({
            succes: true
        });
    }
}

// Sessions controller
class SessionsController extends Controller {
    static async index (req, res) {
        const sessions = await Sessions.select();

        res.json({
            succes: true,
            sessions: sessions
        });
    }

    static async show (req, res) {
        const sessionId = req.params.session_id;

        const session = await Sessions.select(sessionId);

        res.json({
            succes: true,
            session: session
        });
    }

    static async revoke (req, res) {
        const sessionId = req.params.session_id;

        await Sessions.update(sessionId, {
            expires_at: (new Date()).toMysqlString()
        });

        res.json({
            succes: true
        });
    }
}

// User sessions controller
class UserSessionsController extends Controller {
    static async index (req, res) {
        const userId = req.params.user_id;

        const sessions = await Sessions.selectByUser(userId);

        res.json({
            succes: true,
            sessions: sessions
        });
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

// #####################################################
// #################### Page Routes ####################
// #####################################################

// Home
app.all('/', PagesController.home);

// #####################################################
// #################### Auth Routes ####################
// #####################################################

// Login route
app.all(
    '/api/auth/login',
    validate({
        key: Keys.KEY_VALIDATION,
        email: Users.EMAIL_VALIDATION,

        email: Users.EMAIL_VALIDATION,
        password: Users.PASSWORD_VALIDATION
    }),
    AuthController.login
);

// Logout route
app.all(
    '/api/auth/logout',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION
    }),
    AuthController.logout
);

// #####################################################
// #################### User Routes ####################
// #####################################################

// Users index route
app.all(
    '/api/users',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION
    }),
    UsersController.index
);

// Users search route
app.all(
    '/api/users/search',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,

        query: Users.QUERY_VALIDATION
    }),
    UsersController.search
);

// Users show route
app.all(
    '/api/users/{user_id}',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,

        user_id: Users.ID_VALIDATION
    }),
    UsersController.show
);

// Users create route
app.all(
    '/api/admin/users/create',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        session: Users.ADMIN_SESSION_VALIDATION,

        firstname: Users.FIRSTNAME_VALIDATION,
        lastname: Users.LASTNAME_VALIDATION,
        email: Users.EMAIL_VALIDATION
    }),
    UsersController.create
);

// Users edit route
app.all(
    '/api/users/{user_id}/edit',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        user_id: Users.AUTH_ID_VALIDATION,

        user_id: Users.ID_VALIDATION,
        firstname: Users.FIRSTNAME_VALIDATION,
        lastname: Users.LASTNAME_VALIDATION,
        email: Users.EMAIL_VALIDATION,
        active: Users.ACTIVE_VALIDATION
    }),
    UsersController.edit
);

// Users edit password route
app.all(
    '/api/users/{user_id}/edit_password',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        user_id: Users.AUTH_ID_VALIDATION,

        user_id: Users.ID_VALIDATION,
        old_password: Users.OLD_PASSWORD_VALIDATION,
        password: Users.PASSWORD_VALIDATION
    }),
    UsersController.editPassword
);

// #####################################################
// ################### Session Routes ##################
// #####################################################

// Sessions index route
app.all(
    '/api/admin/sessions',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        sessions: Sessions.ADMIN_SESSION_VALIDATION
    }),
    SessionsController.index
);

// Sessions show route
app.all(
    '/api/sessions/{session_id}',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        session_id: Sessions.AUTH_ID_VALIDATION
    }),
    SessionsController.show
);

// Sessions revoke route
app.all(
    '/api/sessions/{session_id}/revoke',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        session_id: Sessions.AUTH_ID_VALIDATION
    }),
    SessionsController.revoke
);

// User Sessions index routes
app.all(
    '/api/users/{user_id}/sessions',
    validate({
        key: Keys.KEY_VALIDATION,
        session: Sessions.SESSION_VALIDATION,
        user_id: Users.AUTH_ID_VALIDATION
    }),
    UserSessionsController.index
);

// #####################################################
// ################### Product Routes ##################
// #####################################################

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
app.all('/api/debt/{debt_id}', DebtController.show); // Rights

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
