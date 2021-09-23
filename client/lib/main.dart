import 'package:flutter/material.dart';

void main() => runApp(App());

class App extends StatelessWidget {
    @override
    Widget build(BuildContext context) {
        return MaterialApp(
            title: 'Strepen',
            home: Home(),
        );
    }
}

class Home extends StatefulWidget {
    @override
    State createState() {
        return _HomeState();
    }
}

class _HomeState extends State {
    int currentIndex = 0;

    final List children = [
        const Center(
            child: const Text('Home'),
        ),
        const Center(
            child: const Text('Stripe'),
        ),
        const Center(
            child: const Text('Profile'),
        ),
    ];

    void onTabTapped(int index) {
        setState(() {
            currentIndex = index;
        });
    }

    @override
    Widget build(BuildContext context) {
        return Scaffold(
            appBar: AppBar(
                title: const Text('Strepen'),
            ),

            body: children[currentIndex],

            bottomNavigationBar: BottomNavigationBar(
                onTap: onTabTapped,
                currentIndex: currentIndex,
                items: [
                    const BottomNavigationBarItem(
                        icon: const Icon(Icons.home),
                        title: const Text('Home'),
                    ),
                    const BottomNavigationBarItem(
                        icon: const Icon(Icons.edit),
                        title: const Text('Stripe'),
                    ),
                    const BottomNavigationBarItem(
                        icon: const Icon(Icons.person),
                        title: const Text('Profile')
                    ),
                ],
            ),
        );
    }
}
