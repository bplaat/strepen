import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:intl/intl.dart';
import '../models/product.dart';
import '../models/transaction.dart';
import '../services/auth_service.dart';
import '../services/settings_service.dart';
import 'home_screen_stripe_tab.dart';

class HomeScreenHistoryTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenHistoryTabState();
  }
}

class _HomeScreenHistoryTabState extends State {
  bool _forceReload = false;

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<List<dynamic>>(
      future: Future.wait([
        AuthService.getInstance().transactions(forceReload: _forceReload),
        SettingsService.getInstance().settings()
      ]),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenHistoryTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.home_history_error),
          );
        } else if (snapshot.hasData) {
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: TransactionList(
              transactions: snapshot.data![0]!,
              settings: snapshot.data![1]!
            )
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

class TransactionList extends StatelessWidget {
  final List<Transaction> transactions;
  final Map<String, dynamic> settings;

  const TransactionList({
    Key? key,
    required this.transactions,
    required this.settings
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return ListView.builder(
      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      itemCount: transactions.length,
      itemBuilder: (context, index) {
        Transaction transaction = transactions[index];
        return Container(
          margin: EdgeInsets.symmetric(vertical: 8),
          child: Card(
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    margin: EdgeInsets.only(bottom: 8),
                    child: Text(transaction.name, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500)),
                  ),

                  if (transaction.type == 'transaction') ...[
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 16),
                      child: Text(lang.home_history_transaction_on(DateFormat('yyyy-MM-dd kk:mm').format(transaction.created_at)), style: TextStyle(color: Colors.grey))
                    ),

                    TransactionProductsAmounts(products: transaction.products!, settings: settings)
                  ],

                  if (transaction.type == 'deposit') ...[
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 8),
                      child: Text(lang.home_history_deposit_on(DateFormat('yyyy-MM-dd kk:mm').format(transaction.created_at)), style: TextStyle(color: Colors.grey))
                    ),

                    Container(
                      width: double.infinity,
                      child: Text('${lang.home_history_amount}: \u20ac ${transaction.price.toStringAsFixed(2)}')
                    )
                  ],

                  if (transaction.type == 'food') ...[
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 8),
                      child: Text(lang.home_history_food_on(DateFormat('yyyy-MM-dd kk:mm').format(transaction.created_at)), style: TextStyle(color: Colors.grey))
                    ),

                    Container(
                      width: double.infinity,
                      child: Text('${lang.home_history_amount}: \u20ac ${transaction.price.toStringAsFixed(2)}')
                    )
                  ]
                ]
              )
            )
          )
        );
      }
    );
  }
}
