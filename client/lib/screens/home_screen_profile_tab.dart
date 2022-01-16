import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class HomeScreenProfileTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenProfileTabState();
  }
}

class _HomeScreenProfileTabState extends State {
  bool _forceReload = false;

  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<User?>(
      future: AuthService.getInstance().user(forceReload: _forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenProfileTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.home_profile_error),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: Center(
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 16),
                        child: SizedBox(
                          width: 192,
                          height: 192,
                          child: Card(
                            clipBehavior: Clip.antiAliasWithSaveLayer,
                            child: Container(
                              decoration: BoxDecoration(
                                image: DecorationImage(
                                  fit: BoxFit.cover,
                                  image: CachedNetworkImageProvider(user.avatar)
                                )
                              )
                            ),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(96),
                            ),
                            elevation: 3
                          )
                        )
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(user.name, style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
                      ),

                      InkWell(
                        onTap: () {
                          setState(() => _forceReload = true);
                        },
                        child: Container(
                          width: double.infinity,
                          margin: EdgeInsets.symmetric(vertical: 8),
                          child: Text(
                            '\u20ac ${user.balance!.toStringAsFixed(2)}',
                            style: TextStyle(fontSize: 24, color: user.balance! < 0 ? Colors.red : null, fontWeight: FontWeight.w500),
                            textAlign: TextAlign.center
                          )
                        )
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 16),
                        child: Row(
                          children: [
                            // Settings button
                            Expanded(
                              flex: 1,
                              child: RaisedButton(
                                onPressed: () {
                                  Navigator.pushNamed(context, '/settings');
                                },
                                color: Colors.pink,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                                padding: EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                                child: Text(lang.home_profile_settings, style: TextStyle(color: Colors.white, fontSize: 18))
                              )
                            ),

                            SizedBox(width: 16),

                            // Logout button
                            Expanded(
                              flex: 1,
                              child: RaisedButton(
                                onPressed: _isLoading ? null : () async {
                                  setState(() => _isLoading = true);
                                  await AuthService.getInstance().logout();
                                  Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
                                },
                                color: Colors.pink,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                                padding: EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                                child: Text(lang.home_profile_logout, style: TextStyle(color: Colors.white, fontSize: 18))
                              )
                            )
                          ]
                        )
                      )
                    ]
                  )
                )
              )
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
