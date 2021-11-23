import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'dart:convert';
import '../config.dart';
import '../models/user.dart';
import '../models/product.dart';
import '../models/transaction.dart';
import '../models/notification.dart';
import 'storage_service.dart';

class AuthService {
  static AuthService? _instance;

  User? _user;

  List<NotificationData>? _unreadNotifications;

  Map<int, List<Transaction>> _transactions = {};

  static AuthService getInstance() {
    if (_instance == null) {
      _instance = AuthService();
    }
    return _instance!;
  }

  Future<bool> login({
    required String email,
    required String password
  }) async {
    final response = await http.post(Uri.parse('${API_URL}/auth/login'), body: {
      'api_key': API_KEY,
      'email': email,
      'password': password
    });
    final data = json.decode(response.body);
    if (!data.containsKey('token')) {
      return false;
    }

    StorageService storage = await StorageService.getInstance();
    await storage.setToken(data['token']);
    await storage.setUserId(data['user_id']);
    return true;
  }

  Future logout() async {
    StorageService storage = await StorageService.getInstance();
    await http.get(Uri.parse('${API_URL}/auth/logout?api_key=${API_KEY}'), headers: {
      'Authorization': 'Bearer ${storage.token!}'
    });
    await storage.setToken(null);
    await storage.setUserId(null);
  }

  Future<User?> user({bool forceReload = false}) async {
    if (_user == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${API_URL}/users/${storage.userId!}?api_key=${API_KEY}'), headers: {
        'Authorization': 'Bearer ${storage.token!}'
      });
      try {
        _user = User.fromJson(json.decode(response.body));
      } catch (exception) {
        return null;
      }
    }
    return _user!;
  }

  Future<List<NotificationData>> unreadNotifications({bool forceReload = false}) async {
    if (_unreadNotifications == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${API_URL}/users/${storage.userId!}/notifications/unread?api_key=${API_KEY}'), headers: {
        'Authorization': 'Bearer ${storage.token!}'
      });
      final notificationsJson = json.decode(response.body)['data'];
      _unreadNotifications = notificationsJson.map<NotificationData>((json) => NotificationData.fromJson(json)).toList();
    }
    return _unreadNotifications!;
  }

  Future readNotification({required String notificationId}) async {
    StorageService storage = await StorageService.getInstance();
    await http.get(Uri.parse('${API_URL}/notifications/${notificationId}/read?api_key=${API_KEY}'), headers: {
      'Authorization': 'Bearer ${storage.token!}'
    });
  }

  Future<List<Transaction>> transactions({int page = 1, bool forceReload = false}) async {
    if (!_transactions.containsKey(page) || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${API_URL}/users/${storage.userId!}/transactions?api_key=${API_KEY}&page=${page}'), headers: {
        'Authorization': 'Bearer ${storage.token!}'
      });
      final transactionsJson = json.decode(response.body)['data'];
      _transactions[page] = transactionsJson.map<Transaction>((json) => Transaction.fromJson(json)).toList();
    }
    return _transactions[page]!;
  }

  Future<bool> createTransaction({required Map<Product, int> productAmounts}) async {
    final body = {
      'api_key': API_KEY,
      'name': 'Mobile transaction on ${DateFormat('yyyy-MM-dd kk:mm:ss').format(DateTime.now())}'
    };

    int index = 0;
    for (Product product in productAmounts.keys) {
      int amount = productAmounts[product]!;
      body['products[${index}][product_id]'] = product.id.toString();
      body['products[${index}][amount]'] = amount.toString();
      if (_user != null) {
        _user!.balance = _user!.balance! - amount * product.price;
      }
      index++;
    }

    StorageService storage = await StorageService.getInstance();
    final response = await http.post(Uri.parse('${API_URL}/transactions'), headers: {
      'Authorization': 'Bearer ${storage.token!}'
    }, body: body);

    final data = json.decode(response.body);
    if (!data.containsKey('transaction')) {
      return false;
    }

    if (_transactions.containsKey(1)) {
      _transactions[1]!.insert(0, Transaction.fromJson(data['transaction']));
    }
    return true;
  }
}
