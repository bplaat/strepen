import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:image_picker/image_picker.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class SettingsChangeAvatarTab extends StatefulWidget {
  @override
  State createState() {
    return _SettingsChangeAvatarTabState();
  }
}

class _SettingsChangeAvatarTabState extends State {
  bool _isLoading = false;

  uploadAvatar() async {
    final lang = AppLocalizations.of(context)!;

    // Show image picker
    setState(() => _isLoading = true);
    XFile? avatar = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (avatar == null) {
      setState(() => _isLoading = false);
      return;
    }

    // Upload image to server
    print('SettingsChangeAvatarTab: Selected image: ${avatar.path}');
    if (await AuthService.getInstance().changeAvatar(avatar: avatar)) {
      // Show success dialog
      setState(() => _isLoading = false);
      showDialog(context: context, builder: (BuildContext context) {
        return AlertDialog(
          title: Text(lang.settings_avatar_success_header),
          content: Text(lang.settings_avatar_success_description),
          actions: [
            TextButton(
            child: Text(lang.settings_avatar_success_ok),
            onPressed: () => Navigator.of(context).pop()
            )
          ]
        );
      });
    } else {
      // Show error dialog
      setState(() => _isLoading = false);
      showDialog(context: context, builder: (BuildContext context) {
        return AlertDialog(
          title: Text(lang.settings_avatar_error_header),
          content: Text(lang.settings_avatar_error_description),
          actions: [
            TextButton(
            child: Text(lang.settings_avatar_success_ok),
            onPressed: () => Navigator.of(context).pop()
            )
          ]
        );
      });
    }
  }

  deleteAvatar() async {
    final lang = AppLocalizations.of(context)!;

    setState(() => _isLoading = true);
    if (await AuthService.getInstance().changeAvatar(avatar: null)) {
      // Show success dialog
      setState(() => _isLoading = false);
      showDialog(context: context, builder: (BuildContext context) {
        return AlertDialog(
          title: Text(lang.settings_avatar_success_header),
          content: Text(lang.settings_avatar_success_description),
          actions: [
            TextButton(
            child: Text(lang.settings_avatar_success_ok),
            onPressed: () {
              Navigator.of(context).pop();
            }
            )
          ]
        );
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return FutureBuilder<User?>(
      future: AuthService.getInstance().user(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('SettingsScreenAvatarTab error: ${snapshot.error}');
          return Center(
            child: Text(lang.settings_avatar_error),
          );
        } else if (snapshot.hasData) {
          User user = snapshot.data!;
          return Container(
            margin: EdgeInsets.only(bottom: 16),
            child: Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 16),
                      child: Text(lang.settings_avatar_header, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
                    ),

                    Container(
                      width: double.infinity,
                      margin: EdgeInsets.only(bottom: 16),
                      child: Text(!user.avatar.contains('default.png') ? lang.settings_avatar_has_avatar : lang.settings_avatar_no_avatar, style: TextStyle(fontSize: 16)),
                    ),

                    // User avatar
                    Container(
                      margin: EdgeInsets.only(bottom: 16),
                      child: SizedBox(
                        width: 256,
                        height: 256,
                        child: Card(
                          clipBehavior: Clip.antiAliasWithSaveLayer,
                          child: Container(
                            decoration: BoxDecoration(
                              image: DecorationImage(
                                fit: BoxFit.cover,
                                image: CachedNetworkImageProvider(user.avatar)
                              )
                            )
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(128),
                          ),
                          elevation: 3
                        )
                      )
                    ),

                    // Upload avatar button
                    Container(
                      margin: EdgeInsets.only(bottom: 16),
                      child: SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isLoading ? null : uploadAvatar,
                          style: ElevatedButton.styleFrom(
                            primary: Colors.pink,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16)
                          ),
                          child: Text(lang.settings_avatar_upload_button, style: TextStyle(color: Colors.white, fontSize: 18))
                        )
                      )
                    ),

                    // Delete avatar button
                    if (!user.avatar.contains('default.png')) ... [
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isLoading ? null : deleteAvatar,
                          style: ElevatedButton.styleFrom(
                            primary: Colors.red,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 16)
                          ),
                          child: Text(lang.settings_avatar_delete_button, style: TextStyle(color: Colors.white, fontSize: 18))
                        )
                      )
                    ]
                  ]
                )
              )
            )
          );
        } else {
          return Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}
