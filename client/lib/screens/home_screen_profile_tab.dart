import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/settings_service.dart';

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
    return FutureBuilder<List<dynamic>>(
      future: Future.wait([
        SettingsService.getInstance().settings(),
        AuthService.getInstance().user(forceReload: _forceReload)
      ]),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenProfileTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.home_profile_error),
          );
        } else if (snapshot.hasData) {
          Map<String, dynamic> settings = snapshot.data![0]!;
          User user = snapshot.data![1]!;
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
                            child: CachedNetworkImage(imageUrl: user.avatar ?? settings['default_user_avatar']),
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
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(lang.home_profile_more, style: TextStyle(fontSize: 16, color: Colors.grey, fontStyle: FontStyle.italic), textAlign: TextAlign.center)
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 16),
                        child: SizedBox(
                          width: double.infinity,
                          child: RaisedButton(
                            onPressed: _isLoading ? null : () async {
                              setState(() => _isLoading = true);
                              await AuthService.getInstance().logout();
                              Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
                            },
                            color: Colors.pink,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                            child: Text(lang.home_profile_logout, style: TextStyle(color: Colors.white, fontSize: 18))
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
          return Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}
