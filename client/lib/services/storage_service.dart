import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static StorageService? _instance;

  late SharedPreferences _prefs;

  static Future<StorageService> getInstance() async {
    if (_instance == null) {
      _instance = StorageService();
      _instance!._prefs = await SharedPreferences.getInstance();
    }
    return _instance!;
  }

  String? get token {
    return _prefs.getString('token');
  }

  void set token(String? token) {
    if (token != null) {
      _prefs.setString('token', token);
    } else {
      _prefs.remove('token');
    }
  }

  int? get userId {
    return _prefs.getInt('user_id');
  }

  void set userId(int? userId) {
    if (userId != null) {
      _prefs.setInt('user_id', userId);
    } else {
      _prefs.remove('user_id');
    }
  }
}
