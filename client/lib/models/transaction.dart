import 'user.dart';
import 'product.dart';

class Transaction {
  final int id;
  final User user;
  final String type;
  final String name;
  final double price;
  final Map<Product, int>? products;
  final DateTime created_at;

  Transaction({
    required this.id,
    required this.type,
    required this.name,
    required this.price,
    required this.user,
    required this.products,
    required this.created_at
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    Map<Product, int>? products = null;
    if (json['products'] != null) {
      products = {};
      for (Map<String, dynamic> productJson in json['products']!) {
        products[Product.fromJson(productJson)] = productJson['amount'];
      }
    }

    return Transaction(
      id: json['id'],
      user: User.fromJson(json['user']),
      type: json['type'],
      name: json['name'],
      price: json['price'].toDouble(),
      products: products,
      created_at: DateTime.parse(json['created_at'])
    );
  }
}
