import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/user.dart';

// https://blog.logrocket.com/building-an-image-picker-in-flutter/

class SettingsChangeThanksTab extends StatefulWidget {
  User user;

  SettingsChangeThanksTab({ required this.user}) {}

  @override
  State createState() {
    return _SettingsChangeThanksTabState(user: user);
  }
}

class _SettingsChangeThanksTabState extends State {
  User user;

  _SettingsChangeThanksTabState({ required this.user}) {}

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return Container(
      margin: EdgeInsets.only(bottom: 16),
      child: Card(
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            children: [
              Container(
                width: double.infinity,
                margin: EdgeInsets.only(bottom: 16),
                child: Text(lang.settings_thanks_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              ),

              Container(
                width: double.infinity,
                margin: EdgeInsets.only(bottom: 0),
                child: Text('Comming soon...', style: TextStyle(fontSize: 16)),
              )
            ]
          )
        )
      )
    );
  }
}
