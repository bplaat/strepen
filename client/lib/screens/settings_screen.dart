import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:intl/intl.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/settings_service.dart';

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

          return Scaffold(
            appBar: AppBar(
              title: Text(lang.settings_header)
            ),

            body: SingleChildScrollView(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    ChangeDetailsForm(user: user)
                  ]
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


class ChangeDetailsForm extends StatefulWidget {
  User user;

  ChangeDetailsForm({ required this.user}) {}

  @override
  State createState() {
    return _ChangeDetailsFormState(user: user);
  }
}

// https://stackoverflow.com/questions/54661567/what-is-the-difference-between-textformfield-and-textfield
// https://medium.flutterdevs.com/date-and-time-picker-in-flutter-72141e7531c
class _ChangeDetailsFormState extends State {
  User user;

  late TextEditingController _firstnameController;
  late TextEditingController _insertionController;
  late TextEditingController _lastnameController;
  late TextEditingController _birthdayController;

  late TextEditingController _emailController;
  late TextEditingController _phoneController;

  late TextEditingController _addressController;
  late TextEditingController _postcodeController;
  late TextEditingController _cityController;

  _ChangeDetailsFormState({ required this.user}) {
    _firstnameController = TextEditingController(text: user.firstname);
    _insertionController = TextEditingController(text: user.insertion);
    _lastnameController = TextEditingController(text: user.lastname);
    _birthdayController = TextEditingController(text: user.birthday != null ? DateFormat('yyyy-MM-dd').format(user.birthday!) : null);

    _emailController = TextEditingController(text: user.email);
    _phoneController = TextEditingController(text: user.phone);

    _addressController = TextEditingController(text: user.address);
    _postcodeController = TextEditingController(text: user.postcode);
    _cityController = TextEditingController(text: user.city);
  }

  @override
  void dispose() {
    _firstnameController.dispose();
    _insertionController.dispose();
    _lastnameController.dispose();
    _birthdayController.dispose();

    _emailController.dispose();
    _phoneController.dispose();

    _addressController.dispose();
    _postcodeController.dispose();
    _cityController.dispose();
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
                child: Text(lang.settings_change_details, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              ),

              // Personal information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_personal_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _firstnameController, label: lang.settings_firstname),
              InputField(controller: _insertionController, label: lang.settings_insertion),
              InputField(controller: _lastnameController, label: lang.settings_lastname),

              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text("Gender TODO", style: TextStyle(fontSize: 14)),
              ),

              InputField(controller: _birthdayController, label: lang.settings_birthday),

              // Contact information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_contact_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _emailController, label: lang.settings_email),
              InputField(controller: _phoneController, label: lang.settings_phone),

              // Address information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_address_info, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _addressController, label: lang.settings_address),
              InputField(controller: _postcodeController, label: lang.settings_postcode),
              InputField(controller: _cityController, label: lang.settings_city),

              // Email notifications
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_email_notifications, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 8),
                child: Text(lang.settings_receive_news, style: TextStyle(fontSize: 14)),
              ),

              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 8),
                child: Text("TODO", style: TextStyle(fontSize: 14)),
              )
            ]
          )
        )
      )
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

// https://blog.logrocket.com/building-an-image-picker-in-flutter/
