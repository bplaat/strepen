import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static StorageService? _instance;

  late SharedPreferences prefs;

  static Future<StorageService> getInstance() async {
    if (_instance == null) {
      _instance = StorageService();
      _instance!.prefs = await SharedPreferences.getInstance();
    }
    return _instance!;
  }
}
