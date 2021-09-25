import 'package:shared_preferences/shared_preferences.dart';
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
      final response = await http.get(Uri.parse(API_URL + '/products?api_key=' + API_KEY), headers: {
        'Authorization': 'Bearer ' + storage.prefs.getString('token')!
      });
      final productsJson = json.decode(response.body)['data'];
      _products = productsJson.map<Product>((json) => Product.fromJson(json)).toList()
        .where((Product product) => product.active).toList();
    }
    return _products!;
  }
}
