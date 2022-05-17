import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import 'storage_service.dart';
import '../models/product.dart';

class ProductsService {
  static ProductsService? _instance;

  List<Product>? _products;

  static ProductsService getInstance() {
    if (_instance == null) {
      _instance = ProductsService();
    }
    return _instance!;
  }

  Future<List<Product>> activeProducts({bool forceReload = false}) async {
    if (_products == null || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${storage.organisation.apiUrl}/products'), headers: {
        'X-Api-Key': storage.organisation.apiKey,
        'Authorization': 'Bearer ${storage.token!}'
      });
      final productsJson = json.decode(response.body)['data'];
      _products = productsJson.map<Product>((json) => Product.fromJson(json)).toList();
      if (_products![0].active != null) {
        _products = _products!.where((Product product) => product.active!).toList();
      }
      _products!.sort((a, b) => b.transactionsCount.compareTo(a.transactionsCount));
    }
    return _products!;
  }
}
