import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/user.dart';

// https://blog.logrocket.com/building-an-image-picker-in-flutter/

class ChangeThanksForm extends StatefulWidget {
  User user;

  ChangeThanksForm({ required this.user}) {}

  @override
  State createState() {
    return _ChangeThanksFormState(user: user);
  }
}

class _ChangeThanksFormState extends State {
  User user;

  _ChangeThanksFormState({ required this.user}) {}

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
                margin: EdgeInsets.only(bottom: 8),
                child: Text(lang.settings_thanks_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              ),

              // Change thanks button
              Container(
                margin: EdgeInsets.symmetric(vertical: 8),
                child: SizedBox(
                  width: double.infinity,
                  child: RaisedButton(
                    onPressed: () {
                      // TODO
                    },
                    color: Colors.pink,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                    child: Text(lang.settings_thanks_header, style: TextStyle(color: Colors.white, fontSize: 18))
                  )
                )
              )
            ]
          )
        )
      )
    );
  }
}
