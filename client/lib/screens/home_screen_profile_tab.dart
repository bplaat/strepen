import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class HomeScreenProfileTab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<User>(
      future: AuthService.getInstance().user(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print(snapshot.error);
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;
          return Padding(
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
                  margin: EdgeInsets.symmetric(vertical: 16),
                  child: Text(user.name, style: TextStyle(fontSize: 32, fontWeight: FontWeight.w500))
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
