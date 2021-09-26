import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'home_screen_posts_tab.dart';
import 'home_screen_stripe_tab.dart';
import 'home_screen_profile_tab.dart';
import '../models/notification.dart';
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
    final lang = AppLocalizations.of(context)!;
    return Scaffold(
      appBar: AppBar(
        title: Text([lang.home_posts, lang.home_stripe, lang.home_profile][_currentPageIndex]),
        actions: [
          NotificationsButton(pageController: _pageController)
        ]
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
            title: Text(lang.home_posts_short),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.edit),
            title: Text(lang.home_stripe_short),
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            title: Text(lang.home_profile_short)
          )
        ]
      )
    );
  }
}

class NotificationsButton extends StatefulWidget {
  final PageController pageController;

  const NotificationsButton({Key? key, required this.pageController}) : super(key: key);

  @override
  State createState() {
    return _NotificationsButtonState(pageController: pageController);
  }
}

class _NotificationsButtonState extends State {
  final PageController pageController;

  bool forceReload = false;

  _NotificationsButtonState({required this.pageController});

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<List<NotificationData>>(
      future: AuthService.getInstance().unreadNotifications(forceReload: forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          List<NotificationData> notifications = snapshot.data!;
          return PopupMenuButton(
            icon: Icon(notifications.length > 0 ? Icons.notifications_on : Icons.notifications_sharp),
            tooltip: lang.home_notifications,
            itemBuilder: (BuildContext context) {
              if (notifications.length > 0) {
                return notifications.take(5).map((NotificationData notification) {
                  if (notification.type == 'new_deposit') {
                    return PopupMenuItem(
                      onTap: () async {
                        await AuthService.getInstance().readNotification(notificationId: notification.id);
                        setState(() => forceReload = true);
                        pageController.animateToPage(2, duration: Duration(milliseconds: 300), curve: Curves.ease);
                      },
                      child: Text(lang.home_new_deposit(notification.data['amount'].toStringAsFixed(2))),
                    );
                  }

                  if (notification.type == 'new_post') {
                    return PopupMenuItem(
                      onTap: () async {
                        await AuthService.getInstance().readNotification(notificationId: notification.id);
                        setState(() => forceReload = true);
                        pageController.animateToPage(0, duration: Duration(milliseconds: 300), curve: Curves.ease);
                      },
                      child: Text(lang.home_new_post),
                    );
                  }

                  if (notification.type == 'low_balance') {
                    return PopupMenuItem(
                      onTap: () async {
                        await AuthService.getInstance().readNotification(notificationId: notification.id);
                        setState(() => forceReload = true);
                        pageController.animateToPage(2, duration: Duration(milliseconds: 300), curve: Curves.ease);
                      },
                      child: Text(lang.home_low_balance(notification.data['balance'].toStringAsFixed(2))),
                    );
                  }

                  return PopupMenuItem(
                    onTap: () async {
                      await AuthService.getInstance().readNotification(notificationId: notification.id);
                      setState(() => forceReload = true);
                    },
                    child: Text(lang.home_unkown_notification),
                  );
                }).toList();
              } else {
                return [
                  PopupMenuItem(
                    onTap: () async {
                      setState(() => forceReload = true);
                    },
                    child: Text(lang.home_unread_notifications_empty, style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic)),
                  )
                ];
              }
            }
          );
        } else {
          if (snapshot.hasError) {
            print('NotificationsButton error: ${snapshot.error}');
          }
          return PopupMenuButton(
            icon: Icon(Icons.notifications_sharp),
            tooltip: lang.home_notifications,
            itemBuilder: (BuildContext context) {
              return [
                PopupMenuItem(
                  onTap: () async {
                    setState(() => forceReload = true);
                  },
                  child: Text(lang.home_unread_notifications_empty, style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic)),
                )
              ];
            }
          );
        }
      }
    );
  }
}
