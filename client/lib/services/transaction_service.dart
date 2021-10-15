import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
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

  Future<bool> create({required Map<Product, int> productAmounts}) async {
    final body = {
      'api_key': API_KEY,
      'name': 'Mobile transaction on ${DateFormat('yyyy-MM-dd kk:mm:ss').format(DateTime.now())}'
    };

    int index = 0;
    for (Product product in productAmounts.keys) {
      int amount = productAmounts[product]!;
      body['products[${index}][product_id]'] = product.id.toString();
      body['products[${index}][amount]'] = amount.toString();
      index++;
    }

    StorageService storage = await StorageService.getInstance();
    final response = await http.post(Uri.parse('${API_URL}/transactions'), headers: {
      'Authorization': 'Bearer ${storage.token!}'
    }, body: body);

    final data = json.decode(response.body);
    if (!data.containsKey('transaction_id')) {
      return false;
    }
    return true;
  }
}
