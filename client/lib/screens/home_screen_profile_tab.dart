import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class HomeScreenProfileTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenProfileTabState();
  }
}

class _HomeScreenProfileTabState extends State {
  bool _forceReload = false;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<User>(
      future: AuthService.getInstance().user(forceReload: _forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print(snapshot.error);
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: Stack(
              children: [
                ListView(),
                Padding(
                  padding: EdgeInsets.all(16.0),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 192,
                        height: 192,
                        margin: EdgeInsets.symmetric(vertical: 16),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          image: DecorationImage(
                            fit: BoxFit.fill,
                            image: user.avatar != null
                              ? CachedNetworkImageProvider(user.avatar!)
                              : AssetImage('assets/avatars/mp.jpg') as ImageProvider
                          )
                        )
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text(user.name, style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
                      ),

                      if (user.balance != null) ...[
                        Container(
                          margin: EdgeInsets.symmetric(vertical: 8),
                          child: Text(
                            '\u20ac ${user.balance!.toStringAsFixed(2)}',
                            style: TextStyle(fontSize: 24, color: user.balance! < 0 ? Colors.red : Colors.black , fontWeight: FontWeight.w500)
                          )
                        )
                      ],

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 8),
                        child: Text('Go to the Strepen website for\nmore settings and options', style: TextStyle(fontSize: 16, color: Colors.grey, fontStyle: FontStyle.italic), textAlign: TextAlign.center)
                      ),

                      Container(
                        margin: EdgeInsets.symmetric(vertical: 16),
                        child: SizedBox(
                          width: double.infinity,
                          child: RaisedButton(
                            onPressed: () async {
                              await AuthService.getInstance().logout();
                              Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
                            },
                            color: Colors.pink,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(48)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                            child: Text('Logout', style: TextStyle(color: Colors.white, fontSize: 18))
                          )
                        )
                      )
                    ]
                  )
                )
              ]
            )
          );
        } else {
          return const Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}
