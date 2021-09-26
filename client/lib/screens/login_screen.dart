import 'package:flutter/material.dart';
import '../services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  @override
  State createState() {
    return _LoginScreenState();
  }
}

class _LoginScreenState extends State {
  final _emailController = TextEditingController();

  final _passwordController = TextEditingController();

  bool _hasError = false;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Center(
          child: SingleChildScrollView(
            child: Padding(
              padding: EdgeInsets.all(16.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    margin: EdgeInsets.symmetric(vertical: 16),
                    child: Text('Login to Strepen', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
                  ),

                  if (_hasError) ...[
                    Container(
                      margin: EdgeInsets.symmetric(vertical: 8),
                      child: Text('Wrong email or password!', style: TextStyle(fontSize: 16, color: Colors.red))
                    )
                  ],

                  Container(
                    margin: EdgeInsets.symmetric(vertical: 8),
                    child: TextField(
                      controller: _emailController,
                      style: TextStyle(fontSize: 18),
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(48)
                        ),
                        contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                        labelText: 'Email'
                      )
                    )
                  ),

                  Container(
                    margin: EdgeInsets.symmetric(vertical: 8),
                    child: TextField(
                      controller: _passwordController,
                      obscureText: true,
                      style: TextStyle(fontSize: 18),
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(48)
                        ),
                        contentPadding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                        labelText: 'Password'
                      )
                    )
                  ),

                  Container(
                    margin: EdgeInsets.symmetric(vertical: 8),
                    child: SizedBox(
                      width: double.infinity,
                      child: RaisedButton(
                        onPressed: () async {
                          if (await AuthService.getInstance().login(
                            email: _emailController.text,
                            password: _passwordController.text
                          )) {
                            Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
                          } else {
                            setState(() => _hasError = true);
                          }
                        },
                        color: Colors.pink,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                        child: Text('Login', style: TextStyle(color: Colors.white, fontSize: 18))
                      )
                    )
                  ),

                  Container(
                    margin: EdgeInsets.symmetric(vertical: 8),
                    child: Text('Made by Bastiaan van der Plaat', style: TextStyle(color: Colors.grey, fontSize: 18))
                  )
                ]
              )
            )
          )
        )
    );
  }
}
