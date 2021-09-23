import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../config.dart';

class Login extends StatefulWidget {
  @override
  State createState() {
    return _LoginState();
  }
}

class _LoginState extends State {
  var error = false;
  final emailController = TextEditingController();
  final passwordController = TextEditingController();

  void login(BuildContext context) async {
    var body = {
      'api_key': API_KEY,
      'email': emailController.text,
      'password': passwordController.text
    };
    var response = await http.post(Uri.parse(API_URL + '/auth/login'), body: body);
    var data = json.decode(response.body);
    if (!data.containsKey('token')) {
      setState(() {
        error = true;
      });
    }

    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', data['token']);
    Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false);
  }

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
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

                if (error) ...[
                  Container(
                    margin: EdgeInsets.symmetric(vertical: 8),
                    child: Text('Wrong email or password!', style: TextStyle(fontSize: 16, color: Colors.red))
                  )
                ],

                Container(
                  margin: EdgeInsets.symmetric(vertical: 8),
                  child: TextField(
                    controller: emailController,
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
                    controller: passwordController,
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
                      onPressed: () {
                        login(context);
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
