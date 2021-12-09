import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:intl/intl.dart';
import '../models/user.dart';
import 'settings_screen.dart';

// https://stackoverflow.com/questions/54661567/what-is-the-difference-between-textformfield-and-textfield
// https://medium.flutterdevs.com/date-and-time-picker-in-flutter-72141e7531c

class ChangeDetailsForm extends StatefulWidget {
  User user;

  ChangeDetailsForm({ required this.user}) {}

  @override
  State createState() {
    return _ChangeDetailsFormState(user: user);
  }
}

class _ChangeDetailsFormState extends State {
  User user;

  late TextEditingController _firstnameController;
  late TextEditingController _insertionController;
  late TextEditingController _lastnameController;
  late TextEditingController _genderController;
  late TextEditingController _birthdayController;

  late TextEditingController _emailController;
  late TextEditingController _phoneController;

  late TextEditingController _addressController;
  late TextEditingController _postcodeController;
  late TextEditingController _cityController;

  late bool _receiveNews;

  _ChangeDetailsFormState({ required this.user}) {
    _firstnameController = TextEditingController(text: user.firstname);
    _insertionController = TextEditingController(text: user.insertion);
    _lastnameController = TextEditingController(text: user.lastname);
    _genderController = TextEditingController(text: user.gender.toString());
    _birthdayController = TextEditingController(text: user.birthday != null ? DateFormat('yyyy-MM-dd').format(user.birthday!) : null);

    _emailController = TextEditingController(text: user.email);
    _phoneController = TextEditingController(text: user.phone);

    _addressController = TextEditingController(text: user.address);
    _postcodeController = TextEditingController(text: user.postcode);
    _cityController = TextEditingController(text: user.city);

    _receiveNews = user.receiveNews!;
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
                child: Text(lang.settings_details_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              ),

              // Personal information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_details_personal_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _firstnameController, label: lang.settings_details_firstname),
              InputField(controller: _insertionController, label: lang.settings_details_insertion),
              InputField(controller: _lastnameController, label: lang.settings_details_lastname),

              InputField(controller: _genderController, label: lang.settings_details_gender),
              InputField(controller: _birthdayController, label: lang.settings_details_birthday),

              // Contact information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_details_contact_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _emailController, label: lang.settings_details_email),
              InputField(controller: _phoneController, label: lang.settings_details_phone),

              // Address information
              Container(
                width: double.infinity,
                margin: EdgeInsets.symmetric(vertical: 16),
                child: Text(lang.settings_details_address_info, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              InputField(controller: _addressController, label: lang.settings_details_address),
              InputField(controller: _postcodeController, label: lang.settings_details_postcode),
              InputField(controller: _cityController, label: lang.settings_details_city),

              // Email notifications
              Container(
                width: double.infinity,
                margin: EdgeInsets.only(top: 16),
                child: Text(lang.settings_details_email_notifications, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
              ),

              Container(
                margin: EdgeInsets.symmetric(vertical: 8),
                child: Row(
                  children: [
                    Text(lang.settings_details_receive_news, style: TextStyle(fontSize: 14)),

                    Spacer(),

                    Switch(
                      activeColor: Colors.pink,
                      value: _receiveNews,
                      onChanged: (bool value) {
                        setState(() => _receiveNews = value);
                      }
                    ),
                  ]
                )
              ),

              // Change details button
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
                    child: Text(lang.settings_details_header, style: TextStyle(color: Colors.white, fontSize: 18))
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
