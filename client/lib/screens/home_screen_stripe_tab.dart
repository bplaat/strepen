import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/product.dart';
import '../services/product_service.dart';
import '../services/transaction_service.dart';

class HomeScreenStripeTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenStripeTabState();
  }
}

class _HomeScreenStripeTabState extends State {
  bool _forceReload = false;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Product>>(
      future: ProductsService.getInstance().activeProducts(forceReload: _forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenStripeTab error: ${snapshot.error}');
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: ProductsList(products: snapshot.data!)
          );
        } else {
          return const Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}

class ProductsList extends StatefulWidget {
  final List<Product> products;

  const ProductsList({Key? key, required this.products}) : super(key: key);

  @override
  State createState() {
    return _ProductsListState(products: products);
  }
}

class _ProductsListState extends State {
  final ScrollController _scrollController = ScrollController();

  final List<Product> products;

  final List<int> _amounts = [];

  bool _isLoading = false;

  _ProductsListState({required this.products});

  @override
  void initState() {
    super.initState();
    for (int i = 0; i < products.length; i++) {
      _amounts.add(0);
    }
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      controller: _scrollController,
      child: Column(
        children: [
          Container(
            margin: EdgeInsets.only(top: 8),
            child: ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: products.length,
              itemBuilder: (context, index) {
                Product product = products[index];
                int amount = _amounts[index];
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
                  subtitle: Text('\u20ac ${product.price.toStringAsFixed(2)}'),
                  trailing: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        onPressed: () {
                          setState(() {
                            if (_amounts[index] > 0) {
                              _amounts[index]--;
                            }
                          });
                        },
                        icon: Icon(Icons.remove),
                        tooltip: 'Decrement'
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(horizontal: 16),
                        child: Text(amount.toString(), style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500))
                      ),

                      IconButton(
                        onPressed: () {
                          setState(() {
                            if (_amounts[index] < 24) {
                              _amounts[index]++;
                            }
                          });
                        },
                        icon: Icon(Icons.add),
                        tooltip: 'Increment'
                      )
                    ]
                  )
                );
              }
            )
          ),

          Container(
            margin: EdgeInsets.all(16),
            child: SizedBox(
              width: double.infinity,
              child: RaisedButton(
                onPressed: _isLoading ? null : () async {
                  setState(() => _isLoading = true);

                  int index = 0;
                  bool? succeeded = await TransactionService.getInstance().create({
                      for (Product product in products) product: _amounts[index++]
                  });

                  if (succeeded == true) {
                    setState(() {
                      for (int i = 0; i < products.length; i++) {
                        _amounts[i] = 0;
                      }
                    });

                    _scrollController.animateTo(0, duration: Duration(milliseconds: 300), curve: Curves.ease);

                    final snackBar = SnackBar(
                      content: Text('Transaction created succesfully'),
                      action: SnackBarAction(
                        label: 'Close',
                        onPressed: () {}
                      )
                    );
                    ScaffoldMessenger.of(context).showSnackBar(snackBar);
                  }

                  if (succeeded == false) {
                    _scrollController.animateTo(0, duration: Duration(milliseconds: 300), curve: Curves.ease);

                    final snackBar = SnackBar(
                      content: Text('An error has occurred!'),
                      action: SnackBarAction(
                        label: 'Close',
                        onPressed: () {}
                      )
                    );
                    ScaffoldMessenger.of(context).showSnackBar(snackBar);
                  }

                  setState(() => _isLoading = false);
                },
                color: Colors.pink,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                child: Text('Stripe', style: TextStyle(color: Colors.white, fontSize: 18))
              )
            )
          )
        ]
      )
    );
  }
}
