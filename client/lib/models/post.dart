import 'user.dart';

class Post {
  final int id;
  final User user;
  final String title;
  final String? image;
  final String body;
  final int likes;
  final bool userLiked;
  final int dislikes;
  final bool userDisliked;
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
      user: User.fromJson(json['user']),
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
}
