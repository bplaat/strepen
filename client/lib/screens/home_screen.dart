import 'package:flutter/material.dart';
import 'home_screen_posts_tab.dart';
import 'home_screen_stripe_tab.dart';
import 'home_screen_profile_tab.dart';
import '../services/auth_service.dart';

class HomeScreen extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenState();
  }
}

class _HomeScreenState extends State {
  int currentIndex = 1;

  void onTabTapped(int index) {
    setState(() => currentIndex = index);
  }

  @override
  void initState() {
    super.initState();
    AuthService.getInstance().user();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(['News posts', 'Stripe', 'Your profile'][currentIndex]),
      ),

      body: Stack(
        children: [
          Offstage(
            offstage: currentIndex != 0,
            child: HomeScreenPostsTab()
          ),
          Offstage(
            offstage: currentIndex != 1,
            child: HomeScreenStripeTab()
          ),
          Offstage(
            offstage: currentIndex != 2,
            child: HomeScreenProfileTab()
          )
        ]
      ),

      bottomNavigationBar: BottomNavigationBar(
        onTap: onTabTapped,
        currentIndex: currentIndex,
        items: [
          BottomNavigationBarItem(
            icon: Icon(Icons.email),
            title: Text('News posts'),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.edit),
            title: Text('Stripe'),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            title: Text('Profile')
          ),
        ],
      ),
    );
  }
}
