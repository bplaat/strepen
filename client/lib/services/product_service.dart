import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/product.dart';
import '../config.dart';
import '../services/storage_service.dart';

Future<List<Product>> fetchActiveProducts() async {
  SharedPreferences storage = await StorageService.getInstance();
  var response = await http.get(Uri.parse(API_URL + '/products?api_key=' + API_KEY), headers: {
    'Authorization': 'Bearer ' + storage.getString('token')!
  });
  var products = json.decode(response.body)['data'];
  return products.map<Product>((json) => Product.fromJson(json))
    .toList()
    .where((Product product) => product.active)
    .toList();
}
