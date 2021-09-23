import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class Login extends StatefulWidget {
    @override
    State createState() {
        return _LoginState();
    }
}

class _LoginState extends State {
    void login() async {
        print(await http.get(Uri.parse('http://strepen.local/')));
        // TODO
    }

    @override
    Widget build(BuildContext context) {
        return Scaffold(
            appBar: AppBar(
                centerTitle: true,
                title: const Text('Login to the Strepen Systeem'),
            ),
            body: Padding(
                padding: EdgeInsets.all(16.0),
                child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                        Container(
                            margin: EdgeInsets.symmetric(vertical: 16),
                            child: TextField(
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(),
                                    labelText: 'Email'
                                )
                            )
                        ),

                        Container(
                            margin: EdgeInsets.symmetric(vertical: 16),
                            child: TextField(
                            obscureText: true,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(),
                                    labelText: 'Password'
                                )
                            )
                        ),

                        SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                                onPressed: login,
                                child: Text('Login')
                            )
                        )
                    ]
                )
            )
        );
    }
}
