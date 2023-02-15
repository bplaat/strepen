import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config.dart';
import '../models/post.dart';
import 'storage_service.dart';

class PostsService {
  static PostsService? _instance;

  Map<int, List<Post>> _posts = {};

  static PostsService getInstance() {
    if (_instance == null) {
      _instance = PostsService();
    }
    return _instance!;
  }

  void clearCache() {
    _posts = {};
  }

  Future<List<Post>> posts({int page = 1, bool forceReload = false}) async {
    if (!_posts.containsKey(page) || forceReload) {
      StorageService storage = await StorageService.getInstance();
      final response = await http.get(Uri.parse('${storage.organisation.apiUrl}/posts?page=${page}'), headers: {
        'X-Api-Key': storage.organisation.apiKey,
        'Authorization': 'Bearer ${storage.token!}'
      });
      final postsJson = json.decode(response.body)['data'];
      _posts[page] = postsJson.map<Post>((json) => Post.fromJson(json)).toList();
    }
    return _posts[page]!;
  }

  Future<Post> like({required int postId}) async {
    StorageService storage = await StorageService.getInstance();
    final response = await http.get(Uri.parse('${storage.organisation.apiUrl}/posts/${postId}/like'), headers: {
      'X-Api-Key': storage.organisation.apiKey,
      'Authorization': 'Bearer ${storage.token!}'
    });
    return Post.fromJson(json.decode(response.body));
  }

  Future<Post> dislike({required int postId}) async {
    StorageService storage = await StorageService.getInstance();
    final response = await http.get(Uri.parse('${storage.organisation.apiUrl}/posts/${postId}/dislike'), headers: {
      'X-Api-Key': storage.organisation.apiKey,
      'Authorization': 'Bearer ${storage.token!}'
    });
    return Post.fromJson(json.decode(response.body));
  }
}
