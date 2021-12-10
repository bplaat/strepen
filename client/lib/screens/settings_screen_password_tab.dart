import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
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

  bool _isLoading = false;
  TextEditingController _currentPasswordController = new TextEditingController();
  String? _currentPasswordError;
  TextEditingController _passwordController = new TextEditingController();
  String? _passwordError;
  TextEditingController _passwordConfirmationController = new TextEditingController();
  String? _passwordConfirmationError;

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

              InputField(controller: _currentPasswordController, label: lang.settings_password_current_password, error: _currentPasswordError, autocorrect: false, obscureText: true),
              InputField(controller: _passwordController, label: lang.settings_password_password, error: _passwordError, autocorrect: false, obscureText: true),
              InputField(controller: _passwordConfirmationController, label: lang.settings_password_password_confirmation, error: _passwordConfirmationError, autocorrect: false, obscureText: true),

              // Change password button
              Container(
                margin: EdgeInsets.symmetric(vertical: 8),
                child: SizedBox(
                  width: double.infinity,
                  child: RaisedButton(
                    onPressed: _isLoading ? null : () async {
                      setState(() => _isLoading = true);
                      Map<String, List<dynamic>>? errors = await AuthService.getInstance().changePassword(
                        currentPassword: _currentPasswordController.text,
                        password: _passwordController.text,
                        passwordConfirmation: _passwordConfirmationController.text
                      );

                      // When there are errors
                      if (errors != null) {
                        setState(() {
                          _currentPasswordError = errors.containsKey('current_password') ? errors['current_password']![0]! : null;
                          _passwordError = errors.containsKey('password') ? errors['password']![0]! : null;
                          _passwordConfirmationError = errors.containsKey('password_confirmation') ? errors['password_confirmation']![0]! : null;
                          _isLoading = false;
                        });
                        return;
                      }

                      // When successfull
                      setState(() {
                        _currentPasswordController.text = '';
                        _currentPasswordError = null;
                        _passwordController.text = '';
                        _passwordError = null;
                        _passwordConfirmationController.text = '';
                        _passwordConfirmationError = null;
                        _isLoading = false;
                      });

                      showDialog(context: context, builder: (BuildContext context){
                        return AlertDialog(
                          title: Text(lang.settings_password_success_header),
                          content: Text(lang.settings_password_success_description),
                          actions: [
                            TextButton(
                              child: Text(lang.settings_password_success_ok),
                              onPressed: () {
                                Navigator.of(context).pop();
                              }
                            )
                          ]
                        );
                      });
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
