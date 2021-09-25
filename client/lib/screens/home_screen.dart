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
  final _pageController = PageController(initialPage: 1);

  int _currentPageIndex = 1;

  @override
  void initState() {
    super.initState();
    AuthService.getInstance().user();
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(['News posts', 'Stripe', 'Your profile'][_currentPageIndex])
      ),

      body: PageView(
        controller: _pageController,
        onPageChanged: (index) {
          setState(() => _currentPageIndex = index);
        },
        children: [
          HomeScreenPostsTab(),
          HomeScreenStripeTab(),
          HomeScreenProfileTab()
        ]
      ),

      bottomNavigationBar: BottomNavigationBar(
        onTap: (index) {
          _pageController.animateToPage(index, duration: Duration(milliseconds: 300), curve: Curves.ease);
          setState(() => _currentPageIndex = index);
        },
        currentIndex: _currentPageIndex,
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
          )
        ]
      )
    );
  }
}
