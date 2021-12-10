import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/settings_service.dart';
import 'settings_screen_avatar_tab.dart';
import 'settings_screen_details_tab.dart';
import 'settings_screen_password_tab.dart';
import 'settings_screen_thanks_tab.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<User?>(
      future: AuthService.getInstance().user(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenProfileTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.home_profile_error),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;
          return DefaultTabController(
            length: 4,
            child: Scaffold(
              appBar: AppBar(
                title: Text(lang.settings_header),
                bottom: TabBar(
                  isScrollable: true,
                  tabs: [
                    Tab(text: lang.settings_details_tab),
                    Tab(text: lang.settings_avatar_tab),
                    Tab(text: lang.settings_thanks_tab),
                    Tab(text: lang.settings_password_tab)
                  ]
                )
              ),

              body: TabBarView(
                children: [
                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: SettingsChangeDetailsTab(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: SettingsChangeAvatarTab(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: SettingsChangeThanksTab(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: SettingsChangePasswordTab(user: user)
                    )
                  )
                ]
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

class InputField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String? error;
  final bool autocorrect;
  final bool obscureText;

  const InputField({
    required this.controller,
    required this.label,
    this.error = null,
    this.autocorrect = true,
    this.obscureText = false,
    Key? key
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(bottom: 16),
      child: TextField(
        controller: controller,
        autocorrect: autocorrect,
        obscureText: obscureText,
        style: TextStyle(fontSize: 16),
        decoration: InputDecoration(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8)
          ),
          contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          labelText: label,
          errorText: error
        )
      )
    );
  }
}
