import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import '../models/product.dart';
import 'storage_service.dart';

class TransactionService {
  static TransactionService? _instance;

  static TransactionService getInstance() {
    if (_instance == null) {
      _instance = TransactionService();
    }
    return _instance!;
  }

  Future<bool?> create(Map<Product, int> products) async {
    final body = {
      'api_key': API_KEY,
      'name': 'Mobile transaction'
    };

    int index = 0;
    for (Product product in products.keys) {
      int amount = products[product]!;
      if (amount > 0) {
        body['products[' + index.toString() + '][product_id]'] = product.id.toString();
        body['products[' + index.toString() + '][amount]'] = amount.toString();
        index++;
      }
    }
    if (body.length == 2) {
      return null;
    }

    StorageService storage = await StorageService.getInstance();
    final response = await http.post(Uri.parse(API_URL + '/transactions'), headers: {
      'Authorization': 'Bearer ' + storage.prefs.getString('token')!
    }, body: body);

    final data = json.decode(response.body);
    if (!data.containsKey('transaction_id')) {
      return false;
    }
    return true;
  }
}
