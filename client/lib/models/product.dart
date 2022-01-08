class Product {
  final int id;
  final String name;
  final String? description;
  final String image;
  final double price;
  final bool alcoholic;
  final bool? active;
  final int transactionsCount;

  Product({
    required this.id,
    required this.name,
    required this.description,
    required this.image,
    required this.price,
    required this.alcoholic,
    required this.active,
    required this.transactionsCount
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      image: json['image'],
      price: json['price'].toDouble(),
      alcoholic: json['alcoholic'],
      active: json['active'],
      transactionsCount: json['transactions_count']
    );
  }
}
