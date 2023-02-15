import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import '../models/organisation.dart';
import '../services/storage_service.dart';
import '../services/auth_service.dart';
import '../config.dart';

class LoginScreen extends StatefulWidget {
  @override
  State createState() {
    return _LoginScreenState();
  }
}

class _LoginScreenState extends State {
  final _organisationController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  bool _isInitialized = false;
  bool _hasError = false;
  bool _isLoading = false;

  @override
  void dispose() {
    _organisationController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  login() async {
    setState(() => _isLoading = true);
    if (await AuthService.getInstance().login(
      email: _emailController.text,
      password: _passwordController.text
    )) {
      Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
    } else {
      setState(() {
        _hasError = true;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    final isMobile = defaultTargetPlatform == TargetPlatform.iOS || defaultTargetPlatform == TargetPlatform.android;
    return Scaffold(
      body: FutureBuilder<StorageService>(
        future: StorageService.getInstance(),
        builder: (context, snapshot) {
          if (snapshot.hasError) {
            print('LoginScreen error: ${snapshot.error}');
            return Center(
              child: Text(lang.login_loading_error)
            );
          } else if (snapshot.hasData) {
            StorageService storage = snapshot.data!;
            if (!_isInitialized) {
              _isInitialized = true;
              _organisationController.text = storage.organisation.name;
            }

            return Center(
              child: SingleChildScrollView(
                child: Container(
                  constraints: BoxConstraints(maxWidth: !isMobile ? 560 : double.infinity),
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(lang.login_header, style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(lang.login_info, style: TextStyle(color: Colors.grey, fontSize: 16), textAlign: TextAlign.center)
                      ),

                      if (_hasError) ...[
                        Container(
                          margin: EdgeInsets.symmetric(vertical: 8),
                          child: Text(lang.login_auth_error, style: TextStyle(fontSize: 16, color: Colors.red))
                        )
                      ],

                      // Organisation input
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: InkWell(
                          customBorder: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(48)
                          ),
                          onTap: () async {
                            showDialog(context: context, builder: (BuildContext context) {
                              return AlertDialog(
                                title: Text(lang.login_organisation),
                                content: Container(
                                  width: 320,
                                  child: ListView.builder(
                                    shrinkWrap: true,
                                    physics: NeverScrollableScrollPhysics(),
                                    itemCount: organisations.length,
                                    itemBuilder: (context, index) {
                                      Organisation organisation = organisations[index];
                                      return ListTile(
                                        title: Text(organisation.name),
                                        subtitle: Text(organisation.host),
                                        onTap: () async {
                                          await storage.setOrganisationId(organisation.id);
                                          _organisationController.text = storage.organisation.name;
                                          Navigator.of(context).pop();
                                        }
                                      );
                                    }
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
                          },
                          child: TextField(
                            controller: _organisationController,
                            autocorrect: false,
                            enabled: false,
                            style: TextStyle(fontSize: 18),
                            decoration: InputDecoration(
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(48)
                              ),
                              contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                              labelText: lang.login_organisation
                            )
                          )
                        )
                      ),

                      // Email input
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: TextFormField(
                          controller: _emailController,
                          onFieldSubmitted: (value) {
                            if (!_isLoading) login();
                          },
                          autocorrect: false,
                          style: TextStyle(fontSize: 18),
                          decoration: InputDecoration(
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(48)
                            ),
                            contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                            labelText: lang.login_email
                          )
                        )
                      ),

                      // Password input
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: TextFormField(
                          controller: _passwordController,
                          onFieldSubmitted: (value) {
                            if (!_isLoading) login();
                          },
                          autocorrect: false,
                          obscureText: true,
                          style: TextStyle(fontSize: 18),
                          decoration: InputDecoration(
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(48)
                            ),
                            contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                            labelText: lang.login_password
                          )
                        )
                      ),

                      // Login button
                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            onPressed: _isLoading ? null : login,
                            style: ElevatedButton.styleFrom(
                              primary: Colors.pink,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                              padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16)
                            ),
                            child: Text(lang.login_login, style: TextStyle(color: Colors.white, fontSize: 18))
                          )
                        )
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(lang.login_reset_password, style: TextStyle(color: Colors.grey, fontSize: 16, fontStyle: FontStyle.italic), textAlign: TextAlign.center)
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(lang.login_footer, style: TextStyle(color: Colors.grey, fontSize: 18))
                      )
                    ]
                  )
                )
              )
            );
          } else {
            return Center(
              child: CircularProgressIndicator()
            );
          }
        }
      )
    );
  }
}
