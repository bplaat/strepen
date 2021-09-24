import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/product.dart';
import '../config.dart';
import '../services/storage_service.dart';

class ProductsService {
  static ProductsService? _instance;

  List<Product>? _products;

  Future<List<Product>> products() async {
    if (_products == null) {
      StorageService storage = await StorageService.getInstance();
      var response = await http.get(Uri.parse(API_URL + '/products?api_key=' + API_KEY), headers: {
        'Authorization': 'Bearer ' + storage.prefs.getString('token')!
      });
      var products = json.decode(response.body)['data'];
      _products = products.map<Product>((json) => Product.fromJson(json)).toList()
        .where((Product product) => product.active).toList();
    }
    return _products!;
  }

  static ProductsService getInstance() {
    if (_instance == null) {
      _instance = ProductsService();
    }
    return _instance!;
  }
}
