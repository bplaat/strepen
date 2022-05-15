import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:intl/intl.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import 'settings_screen.dart';

class SettingsChangeDetailsTab extends StatefulWidget {
  @override
  State createState() {
    return _SettingsChangeDetailsTabState();
  }
}

class _SettingsChangeDetailsTabState extends State {
  bool _isInitialized = false;
  bool _isLoading = false;

  TextEditingController _firstnameController = TextEditingController();
  TextEditingController _insertionController = TextEditingController();
  TextEditingController _lastnameController = TextEditingController();
  String? _firstnameError;
  String? _insertionError;
  String? _lastnameError;
  TextEditingController _genderController = TextEditingController();
  Gender? _gender;
  TextEditingController _birthdayController = TextEditingController();
  DateTime? _birthday;

  TextEditingController _emailController = TextEditingController();
  TextEditingController _phoneController = TextEditingController();
  String? _emailError;
  String? _phoneError;

  TextEditingController _addressController = TextEditingController();
  TextEditingController _postcodeController = TextEditingController();
  TextEditingController _cityController = TextEditingController();
  String? _addressError;
  String? _postcodeError;
  String? _cityError;

  bool _receiveNews = false;

  @override
  void dispose() {
    _firstnameController.dispose();
    _insertionController.dispose();
    _lastnameController.dispose();
    _genderController.dispose();
    _birthdayController.dispose();

    _emailController.dispose();
    _phoneController.dispose();

    _addressController.dispose();
    _postcodeController.dispose();
    _cityController.dispose();
    super.dispose();
  }

  setGender({Gender? gender, bool updateState = true}) {
    final lang = AppLocalizations.of(context)!;
    _gender = gender;
    if (_gender == null) _genderController.text = lang.settings_details_gender_null;
    if (_gender == Gender.male) _genderController.text = lang.settings_details_gender_male;
    if (_gender == Gender.female) _genderController.text = lang.settings_details_gender_female;
    if (_gender == Gender.other) _genderController.text = lang.settings_details_gender_other;
    if (updateState) setState((){});
  }

  changeDetails() async {
    final lang = AppLocalizations.of(context)!;
    setState(() => _isLoading = true);

    Map<String, List<dynamic>>? errors = await AuthService.getInstance().changeDetails(
      firstname: _firstnameController.text,
      insertion: _insertionController.text,
      lastname: _lastnameController.text,
      gender: _gender,
      birthday: _birthday,

      email: _emailController.text,
      phone: _phoneController.text,

      address: _addressController.text,
      postcode: _postcodeController.text,
      city: _cityController.text,

      receiveNews: _receiveNews
    );

    // When there are errors
    if (errors != null) {
      setState(() {
        _firstnameError = errors.containsKey('firstname') ? errors['firstname']![0]! : null;
        _insertionError = errors.containsKey('insertion') ? errors['insertion']![0]! : null;
        _lastnameError = errors.containsKey('lastname') ? errors['lastname']![0]! : null;

        _emailError = errors.containsKey('email') ? errors['email']![0]! : null;
        _phoneError = errors.containsKey('phone') ? errors['phone']![0]! : null;

        _addressError = errors.containsKey('address') ? errors['address']![0]! : null;
        _postcodeError = errors.containsKey('postcode') ? errors['postcode']![0]! : null;
        _cityError = errors.containsKey('city') ? errors['city']![0]! : null;
        _isLoading = false;
      });
      return;
    }

    // When successfull
    setState(() {
      _firstnameError = null;
      _insertionError = null;
      _lastnameError = null;

      _emailError = null;
      _phoneError = null;

      _addressError = null;
      _postcodeError = null;
      _cityError = null;

      _isLoading = false;
    });

    showDialog(context: context, builder: (BuildContext context) {
      return AlertDialog(
        title: Text(lang.settings_details_success_header),
        content: Text(lang.settings_details_success_description),
        actions: [
          TextButton(
            child: Text(lang.settings_details_success_ok),
            onPressed: () => Navigator.of(context).pop()
          )
        ]
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<User?>(
      future: AuthService.getInstance().user(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('SettingsScreenDetailsTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.settings_avatar_error),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;

          if (!_isInitialized) {
            _isInitialized = true;

            _firstnameController.text = user.firstname;
            if (user.insertion != null) {
              _insertionController.text = user.insertion!;
            }
            _lastnameController.text = user.lastname;
            setGender(gender: user.gender, updateState: false);
            if (user.birthday != null) {
              _birthday = user.birthday;
              _birthdayController.text = DateFormat('yyyy-MM-dd').format(_birthday!);
            }

            _emailController.text = user.email!;
            if (user.phone != null) {
              _phoneController.text = user.phone!;
            }

            if (user.address != null) {
              _addressController.text = user.address!;
            }
            if (user.postcode != null) {
              _postcodeController.text = user.postcode!;
            }
            if (user.city != null) {
              _cityController.text = user.city!;
            }

            _receiveNews = user.receiveNews!;
          }

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
                      child: Text(lang.settings_details_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
                    ),

                    // Personal information
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 16),
                      child: Text(lang.settings_details_personal_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
                    ),

                    InputField(
                      controller: _firstnameController,
                      label: lang.settings_details_firstname,
                      error: _firstnameError
                    ),

                    InputField(
                      controller: _insertionController,
                      label: lang.settings_details_insertion,
                      error: _insertionError
                    ),

                    InputField(
                      controller: _lastnameController,
                      label: lang.settings_details_lastname,
                      error: _lastnameError
                    ),

                    InputField(
                      controller: _genderController,
                      label: lang.settings_details_gender,
                      enabled: false,
                      onTap: () async {
                        showDialog(context: context, builder: (BuildContext context) {
                          return AlertDialog(
                            title: Text(lang.settings_details_gender),
                            content: Container(
                              width: double.minPositive,
                              child: ListView(
                                shrinkWrap: true,
                                physics: NeverScrollableScrollPhysics(),
                                children: [
                                  ListTile(
                                    leading: Icon(Icons.person),
                                    title: Text(lang.settings_details_gender_null),
                                    onTap: () {
                                      setGender(gender: null);
                                      Navigator.of(context).pop();
                                    }
                                  ),

                                  ListTile(
                                    leading: Icon(Icons.male),
                                    title: Text(lang.settings_details_gender_male),
                                    onTap: () {
                                      setGender(gender: Gender.male);
                                      Navigator.of(context).pop();
                                    }
                                  ),

                                  ListTile(
                                    leading: Icon(Icons.female),
                                    title: Text(lang.settings_details_gender_female),
                                    onTap: () {
                                      setGender(gender: Gender.female);
                                      Navigator.of(context).pop();
                                    }
                                  ),

                                  ListTile(
                                    leading: Icon(Icons.transgender),
                                    title: Text(lang.settings_details_gender_other),
                                    onTap: () {
                                      setGender(gender: Gender.other);
                                      Navigator.of(context).pop();
                                    }
                                  )
                                ]
                              )
                            ),
                            actions: [
                              TextButton(
                                child: Text(lang.settings_details_gender_cancel),
                                onPressed: () => Navigator.of(context).pop()
                              )
                            ]
                          );
                        });
                      }
                    ),

                    InputField(
                      controller: _birthdayController,
                      label: lang.settings_details_birthday,
                      enabled: false,
                      margin: EdgeInsets.only(bottom: 8),
                      onTap: !user.minor! ? () async {
                        _birthday = await showDatePicker(
                          context: context,
                          initialDate: _birthday != null ? _birthday! : DateTime.now(),
                          initialDatePickerMode: DatePickerMode.day,
                          firstDate: DateTime(1900),
                          lastDate: DateTime.now()
                        );
                        setState(() => _birthdayController.text = _birthday != null ? DateFormat('yyyy-MM-dd').format(_birthday!) : '');
                      } : null
                    ),
                    if (user.minor!) ... [
                      Container(
                        width: double.infinity,
                        margin: EdgeInsets.only(bottom: 16),
                        child: Text(lang.settings_details_birthday_minor, style: TextStyle(fontSize: 14, color: Colors.grey)),
                      )
                    ],

                    // Contact information
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(top: 8, bottom: 16),
                      child: Text(lang.settings_details_contact_info, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.grey)),
                    ),

                    InputField(
                      controller: _emailController,
                      label: lang.settings_details_email,
                      error: _emailError,
                      autocorrect: false
                    ),

                    InputField(
                      controller: _phoneController,
                      label: lang.settings_details_phone,
                      error: _phoneError,
                      autocorrect: false
                    ),

                    // Address information
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(top: 8, bottom: 16),
                      child: Text(lang.settings_details_address_info, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
                    ),

                    InputField(
                      controller: _addressController,
                      label: lang.settings_details_address,
                      error: _addressError
                    ),

                    InputField(
                      controller: _postcodeController,
                      label: lang.settings_details_postcode,
                      error: _postcodeError
                    ),

                    InputField(
                      controller: _cityController,
                      label: lang.settings_details_city,
                      error: _cityError
                    ),

                    // Email notifications
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(top: 8, bottom: 16),
                      child: Text(lang.settings_details_email_notifications, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500, color: Colors.grey)),
                    ),

                    Container(
                      margin: EdgeInsets.only(bottom: 16),
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
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _isLoading ? null : changeDetails,
                        style: ElevatedButton.styleFrom(
                          primary: Colors.pink,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                          padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16)
                        ),
                        child: Text(lang.settings_details_header, style: TextStyle(color: Colors.white, fontSize: 18))
                      )
                    )
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
