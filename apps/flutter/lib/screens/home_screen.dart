import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'home_screen_posts_tab.dart';
import 'home_screen_stripe_tab.dart';
import 'home_screen_history_tab.dart';
import 'home_screen_profile_tab.dart';
import '../models/notification.dart';
import '../services/auth_service.dart';
import '../services/settings_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State createState() {
    return _HomeScreenState();
  }
}

class _HomeScreenState extends State {
  final _pageController = PageController(initialPage: 1);

  int _currentPageIndex = 1;

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
            title: Text([
              lang.home_posts,
              lang.home_stripe,
              lang.home_history,
              lang.home_profile
            ][_currentPageIndex]),
            actions: [NotificationsButton(pageController: _pageController)]),
        body: PageView(
            controller: _pageController,
            onPageChanged: (index) {
              setState(() => _currentPageIndex = index);
            },
            children: const [
              HomeScreenPostsTab(),
              HomeScreenStripeTab(),
              HomeScreenHistoryTab(),
              HomeScreenProfileTab()
            ]),
        bottomNavigationBar: BottomNavigationBar(
            type: BottomNavigationBarType.fixed,
            onTap: (index) {
              _pageController.animateToPage(index,
                  duration: const Duration(milliseconds: 300), curve: Curves.ease);
              setState(() => _currentPageIndex = index);
            },
            currentIndex: _currentPageIndex,
            items: [
              BottomNavigationBarItem(
                  icon: const Icon(Icons.email), label: lang.home_posts_short),
              BottomNavigationBarItem(
                  icon: const Icon(Icons.edit), label: lang.home_stripe_short),
              BottomNavigationBarItem(
                  icon: const Icon(Icons.history), label: lang.home_history_short),
              BottomNavigationBarItem(
                  icon: const Icon(Icons.person), label: lang.home_profile_short)
            ]));
  }
}

class NotificationsButton extends StatefulWidget {
  final PageController pageController;

  const NotificationsButton({Key? key, required this.pageController})
      : super(key: key);

  @override
  State createState() {
    return _NotificationsButtonState(pageController: pageController);
  }
}

class _NotificationsButtonState extends State {
  final PageController pageController;

  bool _forceReload = false;

  _NotificationsButtonState({required this.pageController});

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<List<dynamic>>(
        future: Future.wait([
          AuthService.getInstance()
              .unreadNotifications(forceReload: _forceReload),
          SettingsService.getInstance().settings()
        ]),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            List<NotificationData> notifications = snapshot.data![0]!;
            Map<String, dynamic> settings = snapshot.data![1]!;
            return PopupMenuButton(
                icon: Icon(notifications.isNotEmpty
                    ? Icons.notifications_on
                    : Icons.notifications_sharp),
                tooltip: lang.home_notifications,
                itemBuilder: (BuildContext context) {
                  if (notifications.isNotEmpty) {
                    return notifications
                        .take(5)
                        .map((NotificationData notification) {
                      if (notification.type == NotificationType.newDeposit) {
                        return PopupMenuItem(
                          onTap: () async {
                            await AuthService.getInstance().readNotification(
                                notificationId: notification.id);
                            setState(() => _forceReload = true);
                            pageController.animateToPage(3,
                                duration: const Duration(milliseconds: 300),
                                curve: Curves.ease);
                          },
                          child: Text(lang.home_new_deposit(
                              '${settings['currency_symbol']} ${notification.data['amount'].toStringAsFixed(2)}')),
                        );
                      }

                      if (notification.type == NotificationType.newPost) {
                        return PopupMenuItem(
                          onTap: () async {
                            await AuthService.getInstance().readNotification(
                                notificationId: notification.id);
                            setState(() => _forceReload = true);
                            pageController.animateToPage(0,
                                duration: const Duration(milliseconds: 300),
                                curve: Curves.ease);
                          },
                          child: Text(lang.home_new_post),
                        );
                      }

                      if (notification.type == NotificationType.lowBalance) {
                        return PopupMenuItem(
                          onTap: () async {
                            await AuthService.getInstance().readNotification(
                                notificationId: notification.id);
                            setState(() => _forceReload = true);
                            pageController.animateToPage(3,
                                duration: const Duration(milliseconds: 300),
                                curve: Curves.ease);
                          },
                          child: Text(lang.home_low_balance(
                              '${settings['currency_symbol']} ${notification.data['balance'].toStringAsFixed(2)}')),
                        );
                      }

                      return PopupMenuItem(
                        onTap: () async {
                          await AuthService.getInstance().readNotification(
                              notificationId: notification.id);
                          setState(() => _forceReload = true);
                        },
                        child: Text(lang.home_unknown_notification),
                      );
                    }).toList();
                  } else {
                    return [
                      PopupMenuItem(
                        onTap: () async {
                          setState(() => _forceReload = true);
                        },
                        child: Text(lang.home_unread_notifications_empty,
                            style: const TextStyle(
                                color: Colors.grey,
                                fontStyle: FontStyle.italic)),
                      )
                    ];
                  }
                });
          } else {
            if (snapshot.hasError) {
              print('NotificationsButton error: ${snapshot.error}');
            }
            return PopupMenuButton(
                icon: const Icon(Icons.notifications_sharp),
                tooltip: lang.home_notifications,
                itemBuilder: (BuildContext context) {
                  return [
                    PopupMenuItem(
                      onTap: () async {
                        setState(() => _forceReload = true);
                      },
                      child: Text(lang.home_unread_notifications_empty,
                          style: const TextStyle(
                              color: Colors.grey, fontStyle: FontStyle.italic)),
                    )
                  ];
                });
          }
        });
  }
}
