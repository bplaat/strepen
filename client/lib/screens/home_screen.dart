import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/product.dart';
import '../services/auth_service.dart' as auth;
import '../services/product_service.dart';

// Home
class HomeScreen extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenState();
  }
}

class _HomeScreenState extends State {
  int currentIndex = 1;

  void onTabTapped(int index) {
    setState(() {
      currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(['News posts', 'Stripe', 'Profile'][currentIndex]),
      ),

      body: [
        HomeScreenPostsTab(),
        HomeScreenStripeTab(),
        HomeScreenProfileTab()
      ][currentIndex],

      bottomNavigationBar: BottomNavigationBar(
        onTap: onTabTapped,
        currentIndex: currentIndex,
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.email),
            title: Text('News posts'),
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

// HomeScreenPostsTab
class HomeScreenPostsTab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.all(16.0),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: double.infinity,
            margin: EdgeInsets.symmetric(vertical: 16),
            child: Text('News posts', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500), textAlign: TextAlign.center)
          ),
          Container(
            margin: EdgeInsets.symmetric(vertical: 16),
            child: Text('News posts will come here...', style: TextStyle(fontSize: 16, color: Colors.grey))
          )
        ]
      )
    );
  }
}

// HomeScreenStripeTab
class HomeScreenStripeTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenStripeTabState();
  }
}

class _HomeScreenStripeTabState extends State {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Product>>(
      future: fetchActiveProducts(),
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

// HomeScreenProfileTab
class HomeScreenProfileTab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.all(16.0),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            margin: EdgeInsets.symmetric(vertical: 16),
            child: Text('Profile', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
          ),
          Container(
            margin: EdgeInsets.symmetric(vertical: 16),
            child: SizedBox(
              width: double.infinity,
              child: RaisedButton(
                onPressed: () async {
                  await auth.logout();
                  Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
                },
                color: Colors.pink,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                child: Text('Logout', style: TextStyle(color: Colors.white, fontSize: 18))
              )
            )
          )
        ]
      )
    );
  }
}
