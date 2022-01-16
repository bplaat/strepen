import '../services/post_service.dart';
import 'user.dart';

class Post {
  final int id;
  final User? user;
  final String title;
  final String? image;
  final String body;
  int likes;
  bool userLiked;
  int dislikes;
  bool userDisliked;
  final DateTime created_at;

  Post({
    required this.id,
    required this.user,
    required this.title,
    required this.image,
    required this.body,
    required this.likes,
    required this.userLiked,
    required this.dislikes,
    required this.userDisliked,
    required this.created_at
  });

  factory Post.fromJson(Map<String, dynamic> json) {
    return Post(
      id: json['id'],
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      title: json['title'],
      image: json['image'],
      body: json['body'],
      likes: json['likes'],
      userLiked: json['user_liked'],
      dislikes: json['dislikes'],
      userDisliked: json['user_disliked'],
      created_at: DateTime.parse(json['created_at'])
    );
  }

  Future like() async {
    try {
      Post updatedPost = await PostsService.getInstance().like(postId: this.id);
      this.likes = updatedPost.likes;
      this.userLiked = updatedPost.userLiked;
      this.dislikes = updatedPost.dislikes;
      this.userDisliked = updatedPost.userDisliked;
    } catch (exception) {
      print('LikePost error: ${exception}');
    }
  }

  Future dislike() async {
    try {
      Post updatedPost = await PostsService.getInstance().dislike(postId: this.id);
      this.likes = updatedPost.likes;
      this.userLiked = updatedPost.userLiked;
      this.dislikes = updatedPost.dislikes;
      this.userDisliked = updatedPost.userDisliked;
    } catch (exception) {
      print('DislikePost error: ${exception}');
    }
  }
}
