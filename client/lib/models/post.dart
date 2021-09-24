import 'user.dart';

class Post {
  final int id;
  final User user;
  final String title;
  final String body;
  final DateTime created_at;

  Post({
    required this.id,
    required this.user,
    required this.title,
    required this.body,
    required this.created_at
  });

  factory Post.fromJson(Map<String, dynamic> json) {
    return Post(
      id: json['id'],
      user: User.fromJson(json['user']),
      title: json['title'],
      body: json['body'],
      created_at: DateTime.parse(json['created_at'])
    );
  }
}
