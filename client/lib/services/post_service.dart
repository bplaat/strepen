import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import '../models/post.dart';
import 'storage_service.dart';

class PostsService {
  static PostsService? _instance;

  List<Post>? _posts;

  Future<List<Post>> posts() async {
    if (_posts == null) {
      StorageService storage = await StorageService.getInstance();
      var response = await http.get(Uri.parse(API_URL + '/posts?api_key=' + API_KEY), headers: {
        'Authorization': 'Bearer ' + storage.prefs.getString('token')!
      });
      var postsJson = json.decode(response.body)['data'];
      _posts = postsJson.map<Post>((json) => Post.fromJson(json)).toList();
    }
    return _posts!;
  }

  static PostsService getInstance() {
    if (_instance == null) {
      _instance = PostsService();
    }
    return _instance!;
  }
}
