import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import 'storage_service.dart';

class SettingsService {
  static SettingsService? _instance;

  Map<String, dynamic>? _settings;

  static SettingsService getInstance() {
    if (_instance == null) {
      _instance = SettingsService();
    }
    return _instance!;
  }

  void clearCache() {
    _settings = null;
  }

  Future<Map<String, dynamic>> settings({bool forceReload = false}) async {
    if (_settings == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${storage.organisation.apiUrl}/settings'), headers: {
        'X-Api-Key': storage.organisation.apiKey,
        'Authorization': 'Bearer ${storage.token!}'
      });
      _settings = json.decode(response.body);
    }
    return _settings!;
  }
}
