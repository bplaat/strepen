import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/product.dart';
import '../services/product_service.dart';

// Home
class HomeScreen extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenState();
  }
}

class _HomeScreenState extends State {
  int currentIndex = 0;

  final List children = [
    // Home
    Center(
      child: Text('Home'),
    ),

    // Stripe
    HomeScreenStripe(),

    // Profile
    Center(
      child: Text('Profile'),
    )
  ];

  void onTabTapped(int index) {
    setState(() {
      currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Strepen'),
      ),

      body: children[currentIndex],

      bottomNavigationBar: BottomNavigationBar(
        onTap: onTabTapped,
        currentIndex: currentIndex,
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            title: Text('Home'),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.edit),
            title: Text('Stripe'),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            title: Text('Profile')
          ),
        ],
      ),
    );
  }
}

// HomeStripe
class HomeScreenStripe extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenStripeState();
  }
}

class _HomeScreenStripeState extends State {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Product>>(
      future: fetchProducts(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print(snapshot.error);
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          return ProductsList(products: snapshot.data!);
        } else {
          return const Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}

class ProductsList extends StatelessWidget {
  const ProductsList({Key? key, required this.products}) : super(key: key);

  final List<Product> products;

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      itemCount: products.length,
      itemBuilder: (context, index) {
        Product product = products[index];
        return ListTile(
          leading: product.image != null
            ? CachedNetworkImage(
              width: 56,
              height: 56,
              imageUrl: product.image!
            )
            : Image(
              width: 56,
              height: 56,
              image: AssetImage('assets/products/unkown.png')
            ),
          title: Text(product.name),
          subtitle: Text('\u20ac ${product.price.toStringAsFixed(2)}')
        );
      }
    );
  }
}
