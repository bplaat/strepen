import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/user.dart';
import 'settings_screen.dart';

class ChangePasswordForm extends StatefulWidget {
  User user;

  ChangePasswordForm({ required this.user}) {}

  @override
  State createState() {
    return _ChangePasswordFormState(user: user);
  }
}

class _ChangePasswordFormState extends State {
  User user;

  TextEditingController _currentPasswordController = new TextEditingController();
  TextEditingController _passwordController = new TextEditingController();
  TextEditingController _passwordConfirmationController = new TextEditingController();

  _ChangePasswordFormState({ required this.user}) {}

  @override
  void dispose() {
    _currentPasswordController.dispose();
    _passwordController.dispose();
    _passwordConfirmationController.dispose();
    super.dispose();
  }

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
                child: Text(lang.settings_password_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              ),

              InputField(controller: _currentPasswordController, label: lang.settings_password_current_password),
              InputField(controller: _passwordController, label: lang.settings_password_password),
              InputField(controller: _passwordConfirmationController, label: lang.settings_password_password_confirmation),

              // Change password button
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
                    child: Text(lang.settings_password_header, style: TextStyle(color: Colors.white, fontSize: 18))
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
