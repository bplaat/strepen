import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/product.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/product_service.dart';
import '../services/transaction_service.dart';
import '../config.dart';

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
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<List<Product>>(
      future: ProductsService.getInstance().activeProducts(forceReload: _forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenStripeTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.home_stripe_products_error),
          );
        } else if (snapshot.hasData) {
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: ProductsList(products: snapshot.data!)
          );
        } else {
          return Center(
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
    final lang = AppLocalizations.of(context)!;
    return Center(
      child: SingleChildScrollView(
        controller: _scrollController,
        child: Column(
          children: [
            Container(
              margin: EdgeInsets.only(top: 8),
              child: ListView.builder(
                shrinkWrap: true,
                physics: NeverScrollableScrollPhysics(),
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
                          tooltip: lang.home_stripe_decrement
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
                          tooltip: lang.home_stripe_increment
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
                    final Map<Product, int> productAmounts = {};
                    int index = 0;
                    for (Product product in products) {
                      if (_amounts[index] > 0) {
                        productAmounts[product] = _amounts[index];
                      }
                      index++;
                    };

                    if (productAmounts.length > 0) {
                      setState(() => _isLoading = true);

                      if (await TransactionService.getInstance().create(productAmounts: productAmounts)) {
                        setState(() {
                          for (int i = 0; i < products.length; i++) {
                            _amounts[i] = 0;
                          }
                        });

                        showDialog(context: context, builder: (BuildContext context){
                          return TransactionCreatedDialog(productAmounts: productAmounts);
                        });
                      } else {
                        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                          content: Text(lang.home_stripe_create_error),
                          action: SnackBarAction(
                            label: lang.home_stripe_close,
                            onPressed: () {}
                          )
                        ));
                      }

                      _scrollController.animateTo(0, duration: Duration(milliseconds: 300), curve: Curves.ease);
                      setState(() => _isLoading = false);
                    }
                  },
                  color: Colors.pink,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                  padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                  child: Text(lang.home_stripe_stripe, style: TextStyle(color: Colors.white, fontSize: 18))
                )
              )
            )
          ]
        )
      )
    );
  }
}

class TransactionCreatedDialog extends StatelessWidget {
  final Map<Product, int> productAmounts;

  const TransactionCreatedDialog({Key? key, required this.productAmounts}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<User>(
      future: AuthService.getInstance().user(),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          User user = snapshot.data!;

          int totalAmount = 0;
          double totalPrice = 0;
          for (Product product in productAmounts.keys) {
            int amount = productAmounts[product]!;
            totalAmount += amount;
            totalPrice += product.price * amount;
          }

          return AlertDialog(
            title: Text(lang.home_stripe_created),
            content: Container(
              width: MediaQuery.of(context).size.width * 0.9,
              height: MediaQuery.of(context).size.height * 0.9,
              child: Center(
                child: SingleChildScrollView(
                  child: Column(
                    children: [
                      Container(
                        margin: EdgeInsets.only(bottom: 24),
                        child: SizedBox(
                          width: 256,
                          height: 256,
                          child: Card(
                            clipBehavior: Clip.antiAliasWithSaveLayer,
                            child: CachedNetworkImage(imageUrl: user.thanks ?? '${WEBSITE_URL}/images/thanks/default.gif'),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16.0),
                            ),
                            elevation: 3
                          )
                        )
                      ),

                      Container(
                        margin: EdgeInsets.only(bottom: 24),
                        child: Text(lang.home_stripe_thx, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w500))
                      ),

                      ListView.builder(
                        shrinkWrap: true,
                        physics: NeverScrollableScrollPhysics(),
                        itemCount: productAmounts.length,
                        itemBuilder: (context, index) {
                          Product product = productAmounts.keys.elementAt(index);
                          int amount = productAmounts[product]!;
                          return Container(
                            margin: EdgeInsets.only(bottom: 12),
                            child: Row(
                              children: [
                                Container(
                                  margin: EdgeInsets.only(right: 24),
                                  child: product.image != null
                                    ? CachedNetworkImage(
                                      width: 56,
                                      height: 56,
                                      imageUrl: product.image!
                                    )
                                    : Image(
                                      width: 56,
                                      height: 56,
                                      image: AssetImage('assets/products/unkown.png')
                                    )
                                ),
                                Expanded(
                                  flex: 1,
                                  child: Column(
                                    children: [
                                      Container(
                                        margin: EdgeInsets.only(bottom: 4),
                                        child: SizedBox(
                                          width: double.infinity,
                                          child: Text('${product.name}', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500))
                                        )
                                      ),
                                      SizedBox(
                                        width: double.infinity,
                                        child: Text('${amount}x   \u20ac ${product.price.toStringAsFixed(2)}', style: TextStyle(color: Colors.grey))
                                      )
                                    ]
                                  )
                                ),
                                Text('\u20ac ${(product.price * amount).toStringAsFixed(2)}', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500))
                              ]
                            )
                          );
                        }
                      ),

                      Divider(),

                      Row(
                        children: [
                          Container(
                            margin: EdgeInsets.only(right: 24),
                            child: SizedBox(
                              width: 56,
                              height: 56
                            ),
                          ),
                          Expanded(
                            flex: 1,
                            child: Text('${totalAmount}x', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500))
                          ),
                          Text('\u20ac ${totalPrice.toStringAsFixed(2)}', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500))
                        ]
                      ),

                      Container(
                        margin: EdgeInsets.only(top: 8),
                        child: SizedBox(
                          width: double.infinity,
                          child: RaisedButton(
                            onPressed: () => Navigator.pop(context),
                            color: Colors.pink,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                            child: Text(lang.home_stripe_close, style: TextStyle(color: Colors.white, fontSize: 18))
                          )
                        )
                      )
                    ]
                  )
                )
              )
            )
          );
        } else {
          return AlertDialog(
            title: Text(lang.home_stripe_created),
            content: Center(
              child: CircularProgressIndicator()
            )
          );
        }
      }
    );
  }
}
