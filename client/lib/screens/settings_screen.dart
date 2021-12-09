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
    return FutureBuilder<List<dynamic>>(
      future: Future.wait([
        SettingsService.getInstance().settings(),
        AuthService.getInstance().user()
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

          return DefaultTabController(
            length: 4,
            child: Scaffold(
              appBar: AppBar(
                title: Text(lang.settings_header),
                bottom: TabBar(
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
                      child: ChangeDetailsForm(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: ChangeAvatarForm(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: ChangeThanksForm(user: user)
                    )
                  ),

                  SingleChildScrollView(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: ChangePasswordForm(user: user)
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

  const InputField({
    required this.controller,
    required this.label,
    Key? key
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.symmetric(vertical: 8),
      child: TextField(
        controller: controller,
        style: TextStyle(fontSize: 16),
        decoration: InputDecoration(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8)
          ),
          contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          labelText: label
        )
      )
    );
  }
}
