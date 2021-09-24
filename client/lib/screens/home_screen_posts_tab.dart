import 'package:flutter/material.dart';

class HomeScreenPostsTab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.all(16.0),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: double.infinity,
            margin: EdgeInsets.symmetric(vertical: 16),
            child: Text('News posts', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500), textAlign: TextAlign.center)
          ),
          Container(
            margin: EdgeInsets.symmetric(vertical: 16),
            child: Text('News posts will come here...', style: TextStyle(fontSize: 16, color: Colors.grey))
          )
        ]
      )
    );
  }
}
