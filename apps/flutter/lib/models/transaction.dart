import 'product.dart';

enum TransactionType { transaction, deposit, payment }

TransactionType? transactionTypeFromString(String type) {
  if (type == 'transaction') return TransactionType.transaction;
  if (type == 'deposit') return TransactionType.deposit;
  if (type == 'food' || type == 'payment') {
    return TransactionType.payment; // For backwards compatability
  }
  return null;
}

class Transaction {
  final int id;
  final TransactionType type;
  final String name;
  final double price;
  final Map<Product, int>? products;
  final DateTime createdAt;

  const Transaction({
    required this.id,
    required this.type,
    required this.name,
    required this.price,
    required this.products,
    required this.createdAt,
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    Map<Product, int>? products;
    if (json['products'] != null) {
      products = {};
      for (Map<String, dynamic> productJson in json['products']!) {
        products[Product.fromJson(productJson)] = productJson['amount'];
      }
    }

    return Transaction(
      id: json['id'],
      type: transactionTypeFromString(json['type'])!,
      name: json['name'],
      price: json['price'].toDouble(),
      products: products,
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
