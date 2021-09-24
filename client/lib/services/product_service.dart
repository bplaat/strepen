import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/product.dart';
import '../config.dart';

Future<List<Product>> fetchProducts() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  var response = await http.get(Uri.parse(API_URL + '/products?api_key=' + API_KEY), headers: {
    'Authorization': 'Bearer ' + prefs.getString('token')!
  });
  var products = json.decode(response.body)['data'];
  return products.map<Product>((json) => Product.fromJson(json)).toList();
}
