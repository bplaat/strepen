import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'settings_screen_avatar_tab.dart';
import 'settings_screen_details_tab.dart';
import 'settings_screen_password_tab.dart';
import 'settings_screen_thanks_tab.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    final isMobile = defaultTargetPlatform == TargetPlatform.iOS || defaultTargetPlatform == TargetPlatform.android;
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
              child: Center(
                child: Container(
                  constraints: BoxConstraints(maxWidth: !isMobile ? 560 : double.infinity),
                  padding: EdgeInsets.all(16),
                  child: SettingsChangeDetailsTab()
                )
              )
            ),

            SingleChildScrollView(
              child: Center(
                child: Container(
                  constraints: BoxConstraints(maxWidth: !isMobile ? 560 : double.infinity),
                  padding: EdgeInsets.all(16),
                  child: SettingsChangeAvatarTab()
                )
              )
            ),

            SingleChildScrollView(
              child: Center(
                child: Container(
                  constraints: BoxConstraints(maxWidth: !isMobile ? 560 : double.infinity),
                  padding: EdgeInsets.all(16),
                  child: SettingsChangeThanksTab()
                )
              )
            ),

            SingleChildScrollView(
              child: Center(
                child: Container(
                  constraints: BoxConstraints(maxWidth: !isMobile ? 560 : double.infinity),
                  padding: EdgeInsets.all(16),
                  child: SettingsChangePasswordTab()
                )
              )
            )
          ]
        )
      )
    );
  }
}

class InputField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String? error;
  final bool autocorrect;
  final bool obscureText;
  final bool enabled;
  final void Function()? onTap;
  final EdgeInsets margin;

  const InputField({
    required this.controller,
    required this.label,
    this.error = null,
    this.autocorrect = true,
    this.obscureText = false,
    this.enabled = true,
    this.onTap = null,
    this.margin = const EdgeInsets.only(bottom: 16),
    Key? key
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: margin,
      child: onTap != null ?
        InkWell(
          onTap: onTap,
          child: TextField(
            controller: controller,
            autocorrect: autocorrect,
            obscureText: obscureText,
            enabled: enabled,
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
        )
      : TextField(
        controller: controller,
        autocorrect: autocorrect,
        obscureText: obscureText,
        enabled: enabled,
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
