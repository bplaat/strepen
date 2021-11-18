import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import '../models/user.dart';
import '../models/transaction.dart';
import '../models/notification.dart';
import 'storage_service.dart';

class AuthService {
  static AuthService? _instance;

  User? _user;

  List<NotificationData>? _unreadNotifications;

  List<Transaction>? _transactions;

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
    storage.token = data['token'];
    storage.userId = data['user_id'];
    return true;
  }

  Future logout() async {
    StorageService storage = await StorageService.getInstance();
    await http.get(Uri.parse('${API_URL}/auth/logout?api_key=${API_KEY}'), headers: {
      'Authorization': 'Bearer ${storage.token!}'
    });
    storage.token = null;
    storage.userId = null;
  }

  Future<User> user({bool forceReload = false}) async {
    if (_user == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${API_URL}/users/${storage.userId!}?api_key=${API_KEY}'), headers: {
          'Authorization': 'Bearer ${storage.token!}'
      });
      _user = User.fromJson(json.decode(response.body));
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

  Future<List<Transaction>> transactions({bool forceReload = false}) async {
    if (_transactions == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${API_URL}/users/${storage.userId!}/transactions?api_key=${API_KEY}'), headers: {
        'Authorization': 'Bearer ${storage.token!}'
      });
      final transactionsJson = json.decode(response.body)['data'];
      _transactions = transactionsJson.map<Transaction>((json) => Transaction.fromJson(json)).toList();
    }
    return _transactions!;
  }
}
